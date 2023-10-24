<?php

namespace App\Controller\Admin;

use App\Form\CategoryType;
use App\Entity\CategoryShop;
use App\Repository\CategoryShopRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryShopController extends AbstractController
{
    private $categoryShopRepository;

    public function __construct(CategoryShopRepository $categoryShopRepository)
    {
        $this->categoryShopRepository = $categoryShopRepository;
    }

    #[Route('/category/{categoryId}', name: 'products_by_category')]
    public function productsByCategory($categoryId): Response
    {
        // Récupérez la catégorie spécifiée par son ID
        $category = $this->categoryShopRepository->find($categoryId);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        // Récupérez toutes les catégories pour la navigation
        $categories = $this->categoryShopRepository->findAll();

        // Récupérez la liste des produits de cette catégorie
        $products = $category->getProducts();

        return $this->render('category_shop/productByCategory.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'products' => $products,
        ]);
    }


   // #[Security("is_granted('ROLE_ADMIN') and is_granted('PRODUCT', product)")]
    #[Route('/admin/category/create', name: 'admin_category_create')]
    public function createCategory(Request $request): Response
    {
        $category = new CategoryShop();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('products_by_category', ['categoryId' => $category->getId()]);
        }

        return $this->render('category_shop_create.html.twig', [
            'form' => $form->createView(),
            'h1'=>'Ajouter une categorie'
        ]);
    }

/*

 #[Route('/admin/add/category', name: 'admin_category_create')]
    public function addCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new CategoryShop();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
            'h1' => 'Ajouter une catégorie'
        ]);
    }

*/





    #[Route('/admin/category/edit/{categoryId}', name: 'admin_category_edit')]
    public function editCategory(Request $request, CategoryShop $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('products_by_category', ['categoryId' => $category->getId()]);
        }

        return $this->render('admin/category_shop/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/delete/{categoryId}', name: 'admin_category_delete')]
    public function deleteCategory(Request $request, CategoryShop $category): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('products_by_category', ['categoryId' => $category->getId()]);
    }
}

