<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CommandeController extends AbstractController
{
    /**
     * @Route ("/commande", name="commande")
     */
    public function index( PaginatorInterface $paginator,BuilderInterface $customQrCodeBuilder,Request $request ,PanierRepository $panierRepository, CommandeRepository $repository): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $total=0;
        $sum=0;
        $data=[];
        $utilisateur = $this->getUser();
        $result = $customQrCodeBuilder
            ->size(400)
            ->margin(20)
            ->build();
        $response = new QrCodeResponse($result);
        if($utilisateur)
        {
            $data = $repository->findBy(['utilisateur'=>$utilisateur->getId()]);
            $commande = $paginator->paginate(
                $data,
                $request->query->getInt('page',1),//num page
                4
            );
            $d = $panierRepository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $d->getProduits()->count();
            $dataTarray = $d->getProduits()->toArray();
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
        }

        return $this->render('commande/index.html.twig', [
            'cat'=>$categorie,'data'=>$commande , 'sumP'=>$sum , 'total'=>$total , 'qr'=>$response->getContent()
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

    //********************* Add Commande Mobile ****************************
    /**
     * @Route ("/addCommande")
     */
    public function addCommande(Request $request , SerializerInterface $serializer , EntityManager $em){
        $content = $request->getContent();
        $data = $serializer->deserialize($content,Commande::class,'json');
        $em->persist($data);
        $em->flush();
        return new Response('Commande added seccesfully');
    }
    /**
     * @Route("/GetCommande", name="GetCommande")
     */
    public function getCommandes(CommandeRepository $repository , SerializerInterface $serializer)
    {
        $p = $repository->findAll();
        $dataJson=$serializer->serialize($p,'json',['groups'=>'commande']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
    //************************* Mobile **************************

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

}
