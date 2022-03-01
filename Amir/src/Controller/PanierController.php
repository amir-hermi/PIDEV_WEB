<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\CommandeProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\SerializerInterface;
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
use function Sodium\randombytes_buf;
use function Sodium\randombytes_random16;
use function Sodium\randombytes_uniform;

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
        /*  $form = $this->createFormBuilder($d)
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
     } */

        return $this->render('panier/index.html.twig', [
            'data' => $data , 'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/panierToCommande", name="panierToCommande")
     */

    public function panierToCommande( Session $session,CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UtilisateurRepository $clientRepository ,PanierRepository $repository, Request $request): Response
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
                $prixTot = $prixTot + ($pr->getPrix()*$pr->getQuantite());
            }
            $newCommande->setUtilisateur($clientRepository->find($utilisateur->getId()));
            $newCommande->setStatus("En Attente");
            $newCommande->setReference(random_int(90000,9999999) );
            $newCommande->setMontant($prixTot+7);
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
        $this->addFlash(
            'delP',
            ' Produit supprimer avec succés'
        );

        // dd($newPanier->getProduits());

        //$em->persist($newPanier);

        return $this->redirectToRoute('panier');



    }
    /**
     * @Route("/checkP", name="checkP")
     */
    public function checkP(Session $session,CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UtilisateurRepository $clientRepository ,PanierRepository $repository, Request $request): Response
    {
        $utilisateur = $this->getUser();
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
            return $this->redirectToRoute('paymentMethod');
        }
    }


    /**
     * @Route("/ajoutProduit{id}", name="ajoutProduit")
     */
    public function ajoutProduit( $id,PanierRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        if($utilisateur==null){
            $this->addFlash(
                'login',
                "S'enregistrer d'abord !"
            );
            return $this->redirectToRoute('app_login');
        }
        $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $d->addProduit($produit);
        $em->flush();
        $this->addFlash(
            'addProduit',
            'Produit ajouter avec success !'
        );
        return $this->redirectToRoute('panier');

    }
//******************* panier mobile ***********************
    /**
     * @Route("/remplirPanier", name="remplirPanier")
     */
    public function remplirPanier(EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id'));
        $panier->addProduit($request->query->get('produits'));
        $manager->flush();
        $dataJson=$serializer->serialize($panier,'json',['groups'=>'produit']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
    /**
     * @Route("/removeProduit", name="removeProduit")
     */
    public function removePanier(ProduitRepository $produitRepository,EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id'));
        $produit = $produitRepository->find($request->query->get('produit'));
        $panier->removeProduit($produit);
        $manager->flush();
        $dataJson=$serializer->serialize("produit retiré avec succes");
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }

    /**
     * @Route("/newCommande", name="newCommande")
     */
    public function newCommande(ProduitRepository $produitRepository,UtilisateurRepository $utilisateurRepository,EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id_panier'));
        foreach ($panier->getProduits()->toArray() as $p) {

            $pr = $produitRepository->find($p->getId());
            $prixTot = $prixTot + ($pr->getPrix()*$pr->getQuantite());
        }
        $commande = new Commande();

        $user = $utilisateurRepository->find($request->query->get('id_utilisateur'));
        $commande->setUtilisateur($user);
        $commande->setStatus("En Attente");
        $commande->setReference(random_int(90000,9999999) );
        $commande->setMontant($prixTot+7);
        $commande->setDateCreation(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        foreach ($panier->getProduits()->toArray() as $p) {
            $pr = $produitRepository->find($p->getId());
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setCommande($commande);
            $commandeProduit->setProduit($pr);
            $commandeProduit->setQuantiteProduit($pr->getQuantite());
            $em->persist($commandeProduit);
            $commande->addCommandeProduit($commandeProduit);
            $pr->setQuantite(1);
            $panier->removeProduit($p);
        }
        $em->flush();
        $dataJson=$serializer->serialize($commande,'json',['groups'=>'commande']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
}
