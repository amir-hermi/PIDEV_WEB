<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
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
            'data' => $data,'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/detailleProduit{id}", name="detailleproduit")
     */
    public function detailleProduit(PanierRepository $panierRepository,$id , ProduitRepository $repository,Request $request): Response
    {
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
            ->add('taille',ChoiceType::class,[
                'choices'=>[
                    'S' => 'S',
                    'M'=> 'M',
                    'XL'=> 'XL',
                    'XXL'=> 'XXL'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('panier');
        }
        //dd($p);
        return $this->render('panier/produitDetaille.html.twig',['total'=>$total,'sumP'=>$sum,'produit'=>$p , 'form'=>$form->createView()]);
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
