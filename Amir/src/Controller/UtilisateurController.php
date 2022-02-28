<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/bil", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository, Session $session): Response
    {
        //besoin de droits admin
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }
        else if(in_array('ROLE_USER', $utilisateur->getRoles())){
           if($utilisateur->getEtat()=="Bloquer"){
               $this->addFlash('activer','vous etes bloquer ');
               $session->set("message", "Merci de vous connecter");
             return  $this->redirectToRoute('app_logout');
           }


        }
        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){

            return $this->render('administrateur/dashboard.html.twig', [
                'utilisateurs' => $utilisateurRepository->findAll(),
            ]);

        }
        else if(in_array('ROLE_LIVREUR', $utilisateur->getRoles())){

            return $this->render('livreurr/livreur.html.twig', [
                'utilisateurs' => $utilisateurRepository->findAll(),
            ]);

        }

        else if(in_array('ROLE_FOURNISSEUR', $utilisateur->getRoles())){

            return $this->render('livreurr/livreur.html.twig', [
                'utilisateurs' => $utilisateurRepository->findAll(),
            ]);

        }

        return $this->redirectToRoute('home');
    }







    /*********************partiestat****************************************/
    /**
     * @Route("/nombre", name="countClient")
     */
    public function count(): Response
    {
        $numbre = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->numberOfclient();

        return $this->render('administrateur/dashboard.html.twig',
            ['numbre' => $numbre]);
    }

}