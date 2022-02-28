<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index( PanierRepository $panierRepository,ProduitRepository $repository): Response

    {
        $sum=0;
        $total=0;
        $utilisateur = $this->getUser();
        if ($utilisateur) {
        $d = $panierRepository->findBy(['utilisateur' => $utilisateur->getId()])[0];
        $sum = $d->getProduits()->count();
        $dataTarray = $d->getProduits()->toArray();
        foreach ($dataTarray as $p) {
            $total += ($p->getPrix() * $p->getQuantite());
        }
    }
        $data = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $data,'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/detailleProduit{id}", name="detailleProduit")
     */
    public function detailleProduit(PanierRepository $panierRepository,$id , ProduitRepository $repository,Request $request): Response
    {
        $utilisateur = $this->getUser();
        $d = $panierRepository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $sum = $d->getProduits()->count();
        $dataTarray = $d->getProduits()->toArray();
        $total=0.0;
        foreach ($dataTarray as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        $p = $repository->find($id);
        $form = $this->createFormBuilder($p)
            ->add('quantite',IntegerType::class)
            ->add('taille',TextType::class)
            ->add('Confirmer',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('panier');
        }
        //dd($p);
        return $this->render('panier/produitDetaille.html.twig',['total'=>$total,'sumP'=>$sum,'produit'=>$p , 'form'=>$form->createView()]);
    }
}
