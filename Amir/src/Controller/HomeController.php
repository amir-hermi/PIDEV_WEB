<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Commantaire;
use App\Form\CommantaireType;
use App\Repository\CommantaireRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homep")
     */
    public function base( CommantaireRepository $commantaireRepository,Request $request,ProduitRepository $produitRepository,PanierRepository $repository): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
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
            $commantaire =new Commantaire();
            $data = $commantaireRepository->findAll();
            $form=$this->createForm(CommantaireType::class,$commantaire);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $commantaire->setUtilisateur($utilisateur);

                $em= $this->getDoctrine()->getManager();
                $em->persist($commantaire);
                $em->flush();
                return $this->render('base.html.twig', [
                    'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot,'f' => $form->createView(), 'data' => $data, 'cat' => $categorie,
                ]);
            }
            return $this->render('base.html.twig', [
                'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot,'f' => $form->createView(), 'data' => $data, 'cat' => $categorie,
            ]);
        }else{
            return $this->render('base.html.twig', [
                'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot, 'cat' => $categorie,
            ]);
        }




    }


}
