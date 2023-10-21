<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Entity\Order;
use App\Entity\Recapdetails;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/order/create', name: 'order_index')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, ['user' => $this->getUser()]);
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $cartService->getTotal(),
        ]);
    }




    #[Route('/order/verify', name: 'order_prepare', methods: ['POST'])]
    public function prepareOrder(CartService $cartService, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new \Datetime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('addresses')->getData();
            $deliveryForOrder = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $deliveryForOrder .= '</br>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryForOrder .= ' - ' . $delivery->getCompany();
            }
            $deliveryForOrder .= '</br>' . $delivery->getAddress();
            $deliveryForOrder .= '</br>' . $delivery->getPostaleCode() . ' - ' . $delivery->getCity();
            $deliveryForOrder .= '</br>' . $delivery->getCountry();

            $order = new Order();
            $ref = $datetime->format('dmY') . '-' . uniqid();
            $order->setReference($ref);
            $order->setUser($this->getUser());
            $order->setCreatedAt($datetime);
            $order->setTransporterName($transporter->getTitle());
            $order->setDelivery($transporter->getPrice());
            $order->setReference($deliveryForOrder);
            $order->setIsPaid(0);

            $paymentMethod = $form->get('payment')->getData();
            $order->setMethod($paymentMethod);
            $this->em->persist($order);

            foreach ($cartService->getTotal() as $product) {
                $recapDetails = new Recapdetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getPrice());
                $recapDetails->setTotalRecap($product['product']->getPrice() * $product['quantity']);
                $recapDetails->setProduct($product['product']->getTitle());
                $this->em->persist($recapDetails);
            }

            $this->em->flush();

            return $this->render('order/recap.html.twig', [
                'method' => $order->getMethod(),
                'recapCart' => $cartService->getTotal(),
                'transporter' => $transporter,
                'delivery' => $deliveryForOrder,
                'reference' => $order->getReference(),
            ]);
        }

        return $this->redirectToRoute('cart_index');
    }
}
