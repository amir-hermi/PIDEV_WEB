<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\PanierRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function index(PanierRepository $panierRepository,\Symfony\Component\HttpFoundation\Request $request1,ReclamationRepository $repository): Response
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

        $reclamation =new Reclamation();
        $data = $repository->findAll();
        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request1);
        if($form->isSubmitted() && $form->isValid())
        {

            $em= $this->getDoctrine()->getManager();
            $reclamation->setStatus('En attente');
            $reclamation->setUtilisateur($utilisateur);
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Votre réclamation a bien été envoyé');
            return $this->redirectToRoute('reclamation');
        }
        return $this->render('reclamation/index.html.twig', [
            'cat' => $categorie,'f' => $form->createView(), 'data' => $data,'sumP'=>$sum , 'total'=>$total
        ]);
    }
}
