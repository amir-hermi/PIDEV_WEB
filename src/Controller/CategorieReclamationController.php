<?php

namespace App\Controller;

use App\Entity\CategorieReclamation;
use App\Entity\Commande;
use App\Form\CategorieReclamationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieReclamationController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request1): Response
    {
        $categorie =new CategorieReclamation();
        $form=$this->createForm(CategorieReclamationType::class,$categorie);
        $form->handleRequest($request1);
        if($form->isSubmitted() && $form->isValid())
        {
            $em= $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success','la catégorie a bien été enregistré');
            return $this->redirectToRoute('categorie');
        }
        return $this->render('categorie_reclamation/index.html.twig', [
            'f' => $form->createView(),'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);


    }

}
