<?php

namespace App\Controller;

use App\Entity\Commantaire;
use App\Form\CommantaireType;
use App\Repository\CommantaireRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homep")
     */
    public function base( CommantaireRepository $commantaireRepository,Request $request,ProduitRepository $produitRepository,PanierRepository $repository,PaginatorInterface $paginator): Response
    {
        $total=0;
        $sum=0;
        $prixTot=0;
        $utilisateur = $this->getUser();
        if($utilisateur)
        {
            $data = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
            $sum = $data->getProduits()->count();
            $dataTarray = $data->getProduits()->toArray();
            $total=0.0;
            foreach ($dataTarray as $p){
                $total += ($p->getPrix() * $p->getQuantite());
            }
            $panier = $repository->findBy(['utilisateur' => $utilisateur->getId()])[0];
            $prixTot=0;
            foreach($panier->getProduits()->toArray() as $p){
                $pr = $produitRepository->find($p->getId());
                $prixTot =$prixTot+ $pr->getPrix();
            }
            $commantaire =new Commantaire();
            $cmt = $commantaireRepository->findAll();
            $data = $paginator->paginate(
                $cmt,
                $request->query->getInt('page',1),
                4
            );
            $form=$this->createForm(CommantaireType::class,$commantaire);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $commantaire->setUtilisateur($utilisateur);

                $em= $this->getDoctrine()->getManager();
                $em->persist($commantaire);
                $em->flush();
                return $this->render('base.html.twig', [
                    'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot,'f' => $form->createView(), 'data' => $data,
                ]);
            }
            return $this->render('base.html.twig', [
                'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot,'f' => $form->createView(), 'data' => $data,
            ]);
        }else{
            return $this->render('base.html.twig', [
                'sumP' => $sum,'total'=>$total , 'montant'=>$prixTot,
            ]);
        }




    }


}
