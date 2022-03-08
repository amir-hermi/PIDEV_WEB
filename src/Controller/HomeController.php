<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('home/index.html.twig', [
            'cat' => $categorie,
        ]);
    }

    /**
     * @Route("/rate", name="rate")
     */
    public function rate(): Response
    {
        return $this->render('products/rating.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
