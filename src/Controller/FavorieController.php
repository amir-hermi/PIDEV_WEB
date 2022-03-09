<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Favorie;
use App\Entity\Produit;
use App\Repository\FavorieRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorieController extends AbstractController
{
    /**
     * @Route("/favorie", name="app_favorie")
     */
    public function index(PanierRepository $repository , Request $request , PaginatorInterface $paginator): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        $total=0;
        $data=[];
        $sum=0;
        $utilisateur = $this->getUser();
        // $this->addFlash();
        $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $sum = $d->getProduits()->count();

        $produit = $this->getDoctrine()->getRepository(Favorie::class)->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $data = $produit->getProduit();
        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),//num page
            3
        );
        foreach ($d->getProduits() as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        return $this->render('favorie/index.html.twig', [
            'cat'=>$categorie ,'data' => $produits , 'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/removePF{id}", name="removePF")
     */
    public function removeP( $id,FavorieRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $d->removeProduit($produit);
        $em->flush();


        // dd($newPanier->getProduits());

        //$em->persist($newPanier);

        return $this->redirectToRoute('app_favorie');



    }

    /**
     * @Route("/ajoutProduitF{id}", name="ajoutProduitF")
     */
    public function ajoutProduit( ProduitRepository $produitRepository,$id,FavorieRepository $repository , Request $request): Response
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
        $produit = $produitRepository->find($id);
        $d->addProduit($produit);
        $em->flush();

        return $this->redirectToRoute('app_favorie');

    }
}
