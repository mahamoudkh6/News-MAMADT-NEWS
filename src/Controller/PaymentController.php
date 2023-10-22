<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }

    #[Route('/order/create-session-stripe/{reference}', name: 'payment_stripe', methods: ['POST'])]
    public function index($reference): RedirectResponse
    {
        $productStripe = [];
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            return $this->redirectToRoute('cart_index');
        }

        foreach ($order->getRecapDetails()->getValues() as $product) {
            $productData = $this->em->getRepository(Product::class)->findOneBy(['title' => $product->getProduct()]);

            $productStripe[] =  [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct()
                    ]
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productStripe[] =  [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => ($order->getTransporterPrice()),
                'product_data' => [
                    'name' => $order->getTransporterName()
                ]
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51Nmtr0Ew9VJMLHjd13SdgrUAT8iS9tqAVCqmCPVDtDSG7hVMwKjarKJPAtsJBgFJwqFJAxulxO23NEKn7ptgSVSK00evAJl46g');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' =>  [
                $productStripe
            ],
            'mode' => 'payment',
            'success_url' => $this->generator->generate('/order/success/{reference}',
                ['reference' => $order->getReference()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->generator->generate('/order/error/{reference}',
                ['reference' => $order->getReference()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->em->flush();

        // Redirigez l'utilisateur vers la page de paiement Stripe
        return $this->redirect($checkout_session->url);
    }

    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSuccess($reference, CartService $service): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/error/{reference}', name: 'payment_error')]
    public function stripeError($reference, CartService $service): Response
    {
        return $this->render('order/error.html.twig');
    }
}