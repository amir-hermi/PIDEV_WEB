<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\PanierRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Mukadi\Chart\Builder;
use Mukadi\Chart\Utils\RandomColorFactory;
use Mukadi\Chart\Chart;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;


class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function index(PanierRepository $panierRepository,\Symfony\Component\HttpFoundation\Request $request1,ReclamationRepository $repository): Response
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

        $reclamation =new Reclamation();
        $data = $repository->findAll();
        $reclamation->setDate(new \DateTime('now'));
        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request1);
        if($form->isSubmitted() && $form->isValid())
        {
            $reclamation->setUtilisateur($utilisateur);
            $reclamation->setStatus('En attente');
            $em= $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Votre réclamation a bien été envoyé');
            return $this->redirectToRoute('reclamation');
        }

        return $this->render('reclamation/index.html.twig', [
            'f' => $form->createView(), 'data' => $data,'sumP'=>$sum , 'total'=>$total
        ]);
    }


}
