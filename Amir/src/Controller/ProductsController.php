<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\ProductsType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Knp\Component\Pager\PaginatorInterface;
use mysql_xdevapi\Exception;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index( PanierRepository $panierRepository,ProduitRepository $repository): Response

    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
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
        $allCat = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $data,'sumP'=>$sum , 'total'=>$total , 'categorie'=>$categorie ,'cat'=>$categorie ,'allCat'=>$allCat
        ]);
    }

    /**
     * @Route("/detailleProduit{id}", name="detailleproduit")
     */
    public function detailleProduit(PanierRepository $panierRepository,$id , ProduitRepository $repository,Request $request): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $utilisateur = $this->getUser();
        $d = $panierRepository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $sum = $d->getProduits()->count();
        $dataTarray = $d->getProduits()->toArray();
        $total=0.0;
        foreach ($dataTarray as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        $p = $repository->find($id);
        $form = $this->createFormBuilder($p)
            ->add('quantite',IntegerType::class)
            ->add('taille',ChoiceType::class, [
                'choices' => [
                    'M' => 'M',
                    'S' => 'S',
                    'XS' => 'XS',
                    'L' =>'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    'XXXL' => 'XXXL',

                ],
                'preferred_choices' => ['M', 'L'],
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('panier');
        }
        //dd($p);
        return $this->render('panier/produitDetaille.html.twig',[ 'cat'=>$categorie,'total'=>$total,'sumP'=>$sum,'produit'=>$p , 'form'=>$form->createView()]);
    }
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
    function deleteP($id, PanierRepository $panierRepository )
    {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        dd($produit);
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
            $uploadedFile = $form['image']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/images';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $em=$this->getDoctrine()->getManager();
            $produit->setQuantite(1);
            $produit->setImage($newFilename);
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
     * @Route("/filteproducts/{categorie}/{souscategorie}", name="filteproducts")
     */
    public function ListProduitParSousCategorie( $souscategorie,$categorie ,PanierRepository $panierRepository,ProduitRepository $repository): Response

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
        $allCat = $repository->findAll();
        return $this->render('products/index.html.twig', [
            'data' => $listProduitFiltre,'sumP'=>$sum , 'total'=>$total , 'categorie'=>$categorie1 , 'cat'=>$categorie1 , 'allCat'=>$allCat
        ]);
    }


    //********************* GETALL Produits Mobile *************************
    /**
     * @Route("/AllProducts", name="AllProducts")
     */
    public function getAllProducts(ProduitRepository $produitRepository , SerializerInterface $serializer)
    {
        $p = $produitRepository->findAll();
        $dataJson=$serializer->serialize($p,'json',['groups'=>'produit']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
    /**
     * @Route("/modiferPorduit", name="modiferPorduit")
     */
    public function modiferPorduit( Request $request,ProduitRepository $produitRepository , SerializerInterface $serializer)
    {
        $p = $produitRepository->find($request->get("id"));
        try {
            $p->setQuantite($request->query->get("quantite"));
            $p->setTaille($request->query->get("taille"));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }catch (Exception $ex){
            return new JsonResponse(json_decode($ex->getMessage()));
        }
        return new JsonResponse('produit modifier avec succes');
    }
}
