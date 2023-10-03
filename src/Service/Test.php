<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;
    private SessionInterface $session;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em, SessionInterface $session)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->session = $session;
    }

    public function addToCart(int $id): void
    {
        $cart = $this->session->get('cart', []); // Utilisation de $this->session pour accéder à la session

        if (empty($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }

        $this->session->set('cart', $cart); // Mise à jour du panier dans la session
    }

    public function getTotal()
    {
        $cart = $this->session->get('cart', []);
        $cartData = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->em->getRepository(Product::class)->find($id);

            if ($product instanceof Product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $cartData;
    }
}
