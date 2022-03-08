<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        return $this->render('panier/index.html.twig', [
            'cat' => $categorie,
        ]);
    }
}
