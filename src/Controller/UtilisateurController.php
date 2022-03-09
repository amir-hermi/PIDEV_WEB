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
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/bil", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository, Session $session,TokenGeneratorInterface $token , \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage): Response
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
                $tokenStorage->setToken();
                $this->addFlash('messagegg','you are blocked Sorry ');
                //$session->set("message", "Merci de vous connecter");
                return  $this->redirectToRoute('app_login');
            }


        }
        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){

            return $this->redirectToRoute('dashboard');

        }
        else if(in_array('ROLE_LIVREUR', $utilisateur->getRoles())){

            return $this->redirectToRoute('livreurB');

        }

        else if(in_array('ROLE_FOURNISSEUR', $utilisateur->getRoles())){

            return $this->redirectToRoute("commandestock");

        }
        $utilisateur = $this->getUser();

        $token = $token->generateToken();
        $utilisateur->setActivetoken($token);
        $em=$this->getDoctrine()->getManager();
        $em->flush();

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