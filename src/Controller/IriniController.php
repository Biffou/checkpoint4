<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IriniController extends AbstractController
{
    /**
     * @Route("/Irini", name="Irini_index")
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */

    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository) :Response
    {
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();

        return $this->render('index.html.twig', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}

