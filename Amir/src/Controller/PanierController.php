<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\CommandeProduitRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\String\ByteString;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use function Sodium\randombytes_random16;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(PanierRepository $repository , Request $request): Response
    {
        $total=0;
        $data=[];
        $sum=0;
        $utilisateur = $this->getUser();
       // $this->addFlash();
            $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $data = $d->getProduits()->toArray();
            foreach ($data as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
            //choisir un produit
            //$newPanier = new Panier();
            $form = $this->createFormBuilder($d)
                // ->add("client",EntityType::class, ['class'=> Client::class, 'choice_label' => 'nom'])
                ->add('produits',EntityType::class,[
                    'class'=>Produit::class,
                    'choice_label'=>'image',
                    'multiple'=>true,
                    'mapped'=>false,
                ])
                ->add("Ajouter",SubmitType::class)
                ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid() ) {
                $this->addFlash(
                    'addProduit',
                    'Produit ajouter avec success !'
                );
                $em = $this->getDoctrine()->getManager();
                foreach ($request->request->get('form')['produits'] as $PID){
                    $produit = $this->getDoctrine()->getRepository(Produit::class)->find($PID);
                    $d->addProduit($produit);
                    $em->flush();
                }
                // dd($newPanier->getProduits());

                //$em->persist($newPanier);
                $em->flush();



            return $this->redirectToRoute('panier');
        }

        return $this->render('panier/index.html.twig', [
            'formP' => $form->createView(),'data' => $data , 'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/panierToCommande{idClient}", name="panierToCommande")
     */

    public function panierToCommande( Session $session,CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UtilisateurRepository $clientRepository,$idClient ,PanierRepository $repository, Request $request): Response
    {
        $utilisateur = $this->getUser();
        $newCommande = new Commande();
        $prixTot = 0;
        $panier = $repository->findBy(['utilisateur' => $utilisateur->getId()])[0];
        if ($panier->getProduits()->toArray() == []) {
           // $session->set('empty', 'Remplir votre panier avant de passer une commande !');
           //$session->getFlashBag()->add('empty', 'Remplir votre panier avant de passer une commande !');
            $this->addFlash(
                'empty',
                'Remplir votre panier avant de passer une commande !'
            );
            return $this->redirectToRoute('panier');
        } else {
            foreach ($panier->getProduits()->toArray() as $p) {

                $pr = $produitRepository->find($p->getId());
                $prixTot = $prixTot + $pr->getPrix();
            }
            $newCommande->setUtilisateur($clientRepository->find($utilisateur->getId()));
            $newCommande->setStatus("En Attente");
            $newCommande->setReference(random_bytes(10));
            $newCommande->setMontant($prixTot);
            $newCommande->setDateCreation(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newCommande);
            foreach ($panier->getProduits()->toArray() as $p) {
                $pr = $produitRepository->find($p->getId());
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setCommande($newCommande);
                $commandeProduit->setProduit($pr);
                $commandeProduit->setQuantiteProduit($pr->getQuantite());
                $em->persist($commandeProduit);
                $newCommande->addCommandeProduit($commandeProduit);
                $pr->setQuantite(1);
                $panier->removeProduit($p);
            }
            $this->addFlash(
                'addCommande',
                'Votre commande a été crée avec succes, en attendant la confirmation par téléphone'
            );
            $em->flush();
            return $this->redirectToRoute('commande');
        }
    }
    /**
     * @Route("/removeP{id}", name="removeP")
     */
    public function removeP( $id,PanierRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $em = $this->getDoctrine()->getManager();
                $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
                $d->removeProduit($produit);
                $produit->setQuantite(1);
                $em->flush();

            // dd($newPanier->getProduits());

            //$em->persist($newPanier);

            return $this->redirectToRoute('panier');



    }



}
