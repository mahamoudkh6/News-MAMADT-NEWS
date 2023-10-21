<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/products', name: 'product_list')]
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show(Product $product): Response
    {
        // Vérification de l'existence du produit
        if (!$product) {
            throw $this->createNotFoundException('Produit manquant');
        }

        // Préparation des données du produit
        $productId =$product->getId();
        $productTitle = $product->getTitle();
        $productContent = $product->getContent();
        $productPrice = $product->getPrice();
        $productAttachment = $product->getAttachment();
        $productOrigine = $product->getOrigine();

        return $this->render('product/productShow.html.twig', [
            'product_id'=>$productId,
            'product_title' => $productTitle,
            'product_content' => $productContent,
            'product_price' => $productPrice,
            'product_attachment' => $productAttachment,
            'product_origine' => $productOrigine
        ]);
    }

}
