<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategoriesType;
use App\Form\ProductsType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class CategoriesController extends AbstractController
{
    /**
     * @param CategorieRepository $repository
     * @return Response
     * @Route("/administrateur/categorie",name="listcategorie")
     */

    public function afficheCat(CategorieRepository $repository)
    {

        $categorie = $repository->findAll();
        return $this->render('administrateur/categorie.html.twig',
            ['categorie' => $categorie]);
    }

    /**
     * @Route("/deleteC/{id}",name="deletecategorie")
     */
    function deleteC($id, CategorieRepository $repository)
    {
        $categorie = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('listcategorie');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/Categorie/add", name="addcategorie")
     */
    function add(Request $request) {
        $categorie=new Categorie();
        $form=$this->createForm(CategoriesType::class, $categorie);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('listcategorie');
        }
        return $this->render('categories/add.html.twig',[
            'form'=>$form->createView()
        ]);

    }


    /**
     * @Route("Categorie/update/{id}" , name="updatecategorie")
     */
    function Update(CategorieRepository $repository,$id,Request $request) {
        $categorie=$repository->find($id);
        $form=$this->createForm(CategoriesType::class,$categorie);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("listcategorie");
        }
        return $this->render('categories/update.html.twig',
            [
                'form'=>$form->createView()
            ]);

    }



}

