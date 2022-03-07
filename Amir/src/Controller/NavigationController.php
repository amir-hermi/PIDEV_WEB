<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class NavigationController extends AbstractController
{




    /**
     * @Route("/", name="home")
     */
    public function home(Session $session)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $return = ['cat' => $categorie];

        if($session->has('message'))
        {
            $message = $session->get('message');
            $session->remove('message'); //on vide la variable message dans la session
            $return['message'] = $message; //on ajoute à l'array de paramètres notre message
        }
        return $this->render('home/index.html.twig', $return);
    }

    /**
     * @Route("/livreur", name="livreur")
     */
    public function livreur(Session $session)
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        else if(in_array('ROLE_LIVREUR', $utilisateur->getRoles())){
            return $this->render('administrateur/dashboard.html.twig');
        }
        $session->set("message", "Vous n'avez pas le droit d'acceder à la page admin vous avez été redirigé sur cette page");
        if($session->has('message'))
        {
            $message = $session->get('message');
            $session->remove('message'); //on vide la variable message dans la session
            $return['message'] = $message; //on ajoute à l'array de paramètres notre message
        }
        return $this->redirectToRoute('home', $return);

    }

    /**
     * @Route("/client", name="client")
     */
    public function membre(Session $session , ProduitRepository $produitRepository,PanierRepository $repository)
    {
        $return = [];
        $total=0;
        $sum=0;
        $prixTot=0;
        $utilisateur = $this->getUser();
            $data = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $data->getProduits()->count();
            $dataTarray = $data->getProduits()->toArray();
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
            $panier = $repository->findBy(['utilisateur' => $utilisateur->getId()])[0];
            $prixTot=0;
            foreach($panier->getProduits()->toArray() as $p){
                $pr = $produitRepository->find($p->getId());
                $prixTot =$prixTot+ $pr->getPrix();
            }

        if($session->has('message'))
        {
            $message = $session->get('message');
            $session->remove('message'); //on vide la variable message dans la session
            $return['message'] = $message; //on ajoute à l'array de paramètres notre message
        }
        return $this->render('home/index.html.twig',['sumP' => $sum,'total'=>$total , 'montant'=>$prixTot], $return);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Session $session)
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            return $this->render('administrateur/dashboard.html.twig');
        }
        $session->set("message", "Vous n'avez pas le droit d'acceder à la page admin vous avez été redirigé sur cette page");
        if($session->has('message'))
        {
            $message = $session->get('message');
            $session->remove('message'); //on vide la variable message dans la session
            $return['message'] = $message; //on ajoute à l'array de paramètres notre message
        }
        return $this->redirectToRoute('home', $return);

    }



}