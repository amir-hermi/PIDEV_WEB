<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreurController extends AbstractController
{
    /**
     * @Route("/livreurB", name="livreurB")
     */
    public function index(MissionRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('livreurr/livreur.html.twig', [
            'data' => $data,'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);
    }
    /**
     * @Route ("/missionaccepter/{id}", name="missionaccepter")
     */
    public function accepter (MissionRepository  $repository , $id): Response
    {
        $mission =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $mission->setStatus('Acceptée');

        $manager->flush();
        return $this->redirectToRoute('livreurB');
    }
    /**
     * @Route ("/missionrefuser/{id}", name="missionrefuser")
     */
    public function refuser (MissionRepository $repository , $id): Response
    {
        $mission =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $mission->setStatus('Refusée');

        $manager->flush();
        return $this->redirectToRoute('livreurB');
    }


}
