<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * @Route("/about", name="about")
     */
    public function index(PanierRepository $panierRepository): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        $total=0;
        $sum=0;
        $utilisateur = $this->getUser();
        if($utilisateur)
        {
            $d = $panierRepository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $dataTarray = $d->getProduits()->toArray();
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
        }
        return $this->render('about/index.html.twig', [
            'sumP' => $sum,'total'=>$total , 'cat'=>$categorie
        ]);
    }
}
