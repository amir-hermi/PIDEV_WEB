<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionController extends AbstractController
{
    /**
     * @Route("/mission", name="mission")
     */
    public function AddMision(\Symfony\Component\HttpFoundation\Request $request1): Response
    {

        $mission =new Mission();
        $form=$this->createForm(MissionType::class,$mission);
        $form->handleRequest($request1);
        if($form->isSubmitted() && $form->isValid())
        {
            $em= $this->getDoctrine()->getManager();
            $em->persist( $mission);
            $em->flush();
            $this->addFlash('success','la mission a bien été enregistré');
            return $this->redirectToRoute('mission');
        }
        return $this->render('mission/index.html.twig', [
            'f' => $form->createView(),
        ]);
    }



}
