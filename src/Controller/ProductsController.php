<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\ProductsType;
use App\Repository\MarqueRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{

    /**
     * @param ProduitRepository $repository
     * @return Response
     * @Route("/administrateur/produit",name="listproduit")
     */

    public function affiche(ProduitRepository $repository,PaginatorInterface $paginator , Request $request){
        //$repo=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->findAll();
        $produits = $paginator->paginate(
            $produit,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('administrateur/produit.html.twig',
        ['produits'=>$produits]);
    }

    /**
     * @Route("/deleteP/{id}",name="deleteproduit")
     */
    function deleteP($id, ProduitRepository $repository)
    {
        $produit = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('listproduit');

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/Products/add", name="addproduct")
     */
    function add(Request $request) {
    $produit=new Produit();
    $form=$this->createForm(ProductsType::class, $produit);
    $form->add('Ajouter',SubmitType::class);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid() ) {

        $em=$this->getDoctrine()->getManager();
        $em->persist($produit);
        $em->flush();
        return $this->redirectToRoute('listproduit');
        }
    return $this->render('products/add.html.twig',[
        'form'=>$form->createView()
    ]);
    }

    /**
     * @Route("Products/update/{id}" , name="updateproduct")
     */
    function Update(ProduitRepository $repository,$id,Request $request) {
        $produit=$repository->find($id);
        $form=$this->createForm(ProductsType::class,$produit);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("listproduit");
        }
        return $this->render('Products/update.html.twig',
        [
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/products", name="products")
     */
    public function index( PanierRepository $panierRepository,ProduitRepository $repository): Response

    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

       // foreach ($categorie as $c){
         //   dd($c->getSousCategories()->toArray());
        //}

        $sum=0;
        $total=0;
        $utilisateur = $this->getUser();
        if ($utilisateur) {
            $d = $panierRepository->findBy(['utilisateur' => $utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $dataTarray = $d->getProduits()->toArray();
            foreach ($dataTarray as $p) {
                $total += ($p->getPrix() * $p->getQuantite());
            }
        }
        $data = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $data,'sumP'=>$sum , 'total'=>$total , 'categorie'=>$categorie , 'cat'=>$categorie
        ]);
    }

    /**
     * @Route("/filteproducts/{categorie}/{souscategorie}", name="filteproducts")
     */
    public function ListProduitParSousCategorie( Request $request,$souscategorie,$categorie ,PanierRepository $panierRepository,ProduitRepository $repository): Response

    {
        $listProduitFiltre = $repository->ListProduitParSousCategorie($categorie,$souscategorie);
        $categorie1 = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        // foreach ($categorie as $c){
        //   dd($c->getSousCategories()->toArray());
        //}

        $sum=0;
        $total=0;
        $utilisateur = $this->getUser();
        if ($utilisateur) {
            $d = $panierRepository->findBy(['utilisateur' => $utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $dataTarray = $d->getProduits()->toArray();
            foreach ($dataTarray as $p) {
                $total += ($p->getPrix() * $p->getQuantite());
            }
        }
        if ($request->isMethod('post')){
            $val = $request->request->get("value");
            $listProduitFiltre = $repository->rechercheParNometMarque($val);
        }
        //$data = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $listProduitFiltre,'sumP'=>$sum , 'total'=>$total , 'categorie'=>$categorie1 , 'cat'=>$categorie1
        ]);
    }




}
