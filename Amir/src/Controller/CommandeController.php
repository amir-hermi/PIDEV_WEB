<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CommandeController extends AbstractController
{
    /**
     * @Route ("/commande", name="commande")
     */
    public function index(Request $req ,PanierRepository $panierRepository, CommandeRepository $repository): Response
    {
        $total=0;
        $sum=0;
        $data=[];
        $utilisateur = $this->getUser();
        if($utilisateur)
        {
            $data = $repository->findBy(['utilisateur'=>$utilisateur->getId()]);
            $d = $panierRepository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $dataTarray = $d->getProduits()->toArray();
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
        }

        return $this->render('commande/index.html.twig', [
            'data'=>$data , 'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route ("/commandeDelete/{id}", name="commandeDelete")
     */
    public function delete(CommandeRepository $repository , $id): Response
    {
        $comm =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comm);
        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('commande');
    }

}
