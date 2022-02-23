<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request1): Response
    {

        $reclamation =new Reclamation();
        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request1);
        if($form->isSubmitted() && $form->isValid())
        {
            $em= $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Votre réclamation a bien été envoyé');
            return $this->redirectToRoute('reclamation');
        }
        return $this->render('reclamation/index.html.twig', [
            'f' => $form->createView(),
        ]);
    }
}
