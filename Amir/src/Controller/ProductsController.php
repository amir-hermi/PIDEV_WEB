<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index(ProduitRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $data,
        ]);
    }
}
