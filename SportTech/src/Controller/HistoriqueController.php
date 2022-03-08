<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PanierRepository;
use App\Repository\ReclamationRepository;
use Container1liGX57\PaginatorInterface_82dac15;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    /**
     * @Route("/historique", name="historique")
     */
    public function index( PanierRepository $panierRepository,ReclamationRepository $repository, PaginatorInterface $paginator,Request $request): Response
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
        $reclamation = $repository->findAll();
        $data = $paginator->paginate(
            $reclamation,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('historique/index.html.twig', [
            'data' => $data,'sumP'=>$sum , 'total'=>$total,'cat' => $categorie
        ]);
    }

}
