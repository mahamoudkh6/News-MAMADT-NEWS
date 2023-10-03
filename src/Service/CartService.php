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

        public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
        {
            $this->requestStack = $requestStack;
            $this->em = $em;
        }

        public function addToCart(int $id): void
        {
            $card = $this->requestStack->getSession()->get('card', []);
            if (empty($card[$id])) {
                $card[$id] = 1;
            } else {
                $card[$id]++;
            }
            $this->getSession()->set('cart', $card);
    }

        public function getTotal()
        {
        $cart = $this->getSession()->set('cart');
        $cardData = [];
        foreach($cart as $id =>$quantity){
        $product = $this->em->getRepository(Product::class)->findAll(['id' => $id]);
        if(!$product){

        }
        $cardData[] =[
        'product' => $product,
        'quantity' => $quantity
        ];
        }
        return $cardData;
        }


        private function getSession(): SessionInterface
        {
        return $this->requestStack->getSession();
        }

}