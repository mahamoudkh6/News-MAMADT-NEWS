<?php

namespace App\Controller;

use App\Repository\CategoryShopRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoryShopRepository $categoryShopRepository)
    {
        // Récupérez la liste des catégories depuis la base de données
        $categories = $categoryShopRepository->findAll();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }





}
