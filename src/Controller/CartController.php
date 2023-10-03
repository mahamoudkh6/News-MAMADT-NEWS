<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart_index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig');
    }



    #[Route('/mon-panier/add/{id}', name: 'cart_add', requirements: ['id' => '\d+'])]
    public function addToCart(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('cart_index');
    }


}






