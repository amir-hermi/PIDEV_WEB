<?php

namespace App\Controller;

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
    public function index(ReclamationRepository $repository, PaginatorInterface $paginator,Request $request): Response
    {
        $reclamation = $repository->findAll();
        $data = $paginator->paginate(
            $reclamation,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('historique/index.html.twig', [
            'data' => $data,
        ]);
    }

}
