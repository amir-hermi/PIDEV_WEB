<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class CommandeController extends AbstractController
{
    /**
     * @Route ("/commande", name="commande")
     */
    public function index(Request $req , CommandeRepository $repository): Response
    {
        $data = $repository->findAll();
        $newCommande = new Commande( );
        $form = $this->createFormBuilder($newCommande)
            ->add("status",TextType::class)
            ->add("montant",IntegerType::class)
            ->add("date_creation",\Symfony\Component\Form\Extension\Core\Type\DateType::class)
            ->add("reference",TextType::class)
            ->add("client",EntityType::class, ['class'=>Client::class , 'choice_label' => 'nom'])
            ->add("Ajouter",SubmitType::class)
            ->getForm();
        $form->handleRequest($req);
        if( $form->isSubmitted() && $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('commande');
        }
        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),'data'=>$data
        ]);
    }




}
