<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProductsType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param ProduitRepository $repository
     * @return Response
     * @Route("/administrateur/produit",name="listproduit")
     */

    public function affiche(ProduitRepository $repository){
        //$repo=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->findAll();
        return $this->render('administrateur/produit.html.twig',
        ['produits'=>$produit]);
    }

    /**
     * @Route("/deleteP/{id}",name="deleteproduit")
     */
    function deleteP($id, ProduitRepository $repository)
    {
        $produit = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('listproduit');

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/Products/add", name="addproduct")
     */
    function add(Request $request) {
    $produit=new Produit();
    $form=$this->createForm(ProductsType::class, $produit);
    $form->add('Ajouter',SubmitType::class);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid() ) {
        $em=$this->getDoctrine()->getManager();
        $em->persist($produit);
        $em->flush();
        return $this->redirectToRoute('listproduit');
        }
    return $this->render('products/add.html.twig',[
        'form'=>$form->createView()
    ]);
    }

    /**
     * @Route("Products/update/{id}" , name="updateproduct")
     */
    function Update(ProduitRepository $repository,$id,Request $request) {
        $produit=$repository->find($id);
        $form=$this->createForm(ProductsType::class,$produit);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("listproduit");
        }
        return $this->render('Products/update.html.twig',
        [
            'form'=>$form->createView()
        ]);

    }
}
