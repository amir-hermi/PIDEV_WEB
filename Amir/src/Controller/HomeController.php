<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homep")
     */
    public function base(ProduitRepository $produitRepository,PanierRepository $repository): Response
    {
        $total=0;
        $sum=0;
        $prixTot=0;
        $utilisateur = $this->getUser();
        if($utilisateur)
        {
            $data = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $data->getProduits()->count();
            $dataTarray = $data->getProduits()->toArray();
            $total=0.0;
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
            $panier = $repository->findBy(['utilisateur' => $utilisateur->getId()])[0];
            $prixTot=0;
            foreach($panier->getProduits()->toArray() as $p){
                $pr = $produitRepository->find($p->getId());
                $prixTot =$prixTot+ $pr->getPrix();
            }
        }



        return $this->render('base.html.twig', [
            'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot
        ]);
    }
}
