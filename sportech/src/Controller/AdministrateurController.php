<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrateurController extends AbstractController
{
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function index(): Response
    {
        return $this->render('administrateur/dashboard.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    /**
     * @Route("/administrateur/commande", name="listcommande")
     */
    public function listCommande(CommandeRepository $repository): Response
    {
        $data = $repository->findBy(['client'=>1]);
        return $this->render('administrateur/commande.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/administrateur/client", name="listclient")
     */
    public function listClient(): Response
    {
        return $this->render('administrateur/client.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    /**
     * @Route("/administrateur/produit", name="listproduit")
     */
    public function listProduit(): Response
    {
        return $this->render('administrateur/produit.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    /**
     * @Route("/administrateur/updateCommande{idP}", name="updateCommande")
     */
    public function updateCommande($idP,CommandeRepository $repository , Request $request): Response
    {
        $comm = $repository->find($idP);
        $form = $this->createFormBuilder($comm)
            ->add('status',ChoiceType::class,[
                'choices'  => [
                    'En Attente' => 'En attente',
                    'Annulée' => 'Annulée',
                    'Confirmé' => 'Confirmée',
                    'En cours de preparation' => 'En cours de preparation',
                    'Livraison en cours' => 'Livraison en cours',
                    'Livrée' => 'Livrée',
                ]])
            ->add('Confirmer',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listcommande');
        }
        return $this->render('administrateur/updateCommande.html.twig', [
            'formU' => $form->createView(),
        ]);
    }
}
