<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\Reclamation;
use App\Form\MissionType;
use App\Form\ReclamationType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\FournisseurRepository;
use App\Repository\MissionRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrateurController extends AbstractController
{
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function index(): Response
    {
        return $this->render('administrateur/dashboard.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    /**
     * @Route("/administrateur/commande", name="listcommande")
     */
    public function listCommande(CommandeRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('administrateur/commande.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/administrateur/client", name="listclient")
     */
    public function listClient( ClientRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('administrateur/client.html.twig', [
            'data' => $data,
        ]);

    }

    /**
     * @Route ("/clientDelete/{id}", name="clientDelete")
     */
    public function delete(ClientRepository $repository , $id): Response
    {
        $client =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($client);
        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listclient');
    }

    /**
     * @Route ("/clientbloque/{id}", name="clientbloque")
     */
    public function bloque(ClientRepository $repository , $id): Response
    {
        $client =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $client->setEtat('Bloquer');


        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listclient');
    }
    /**
     * @Route ("/clientdebloque/{id}", name="clientdebbloque")
     */
    public function debloque(ClientRepository $repository , $id): Response
    {
        $client =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $client->setEtat('Debloquer');


        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listclient');
    }

    /**
     * @Route("/administrateur/Fou", name="listfournisseur")
     */
    public function listfournisseur(FournisseurRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('administrateur/fournisseur.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/administrateur/mission", name="listmission")
     */
    public function listMission(MissionRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('administrateur/mission.html.twig', [
            'data' => $data,
        ]);

    }
    /**
     * @Route("/administrateur/reclamation", name="listreclamation")
     */
    public function listReclamation(ReclamationRepository $repository): Response
    {
        $data = $repository->findAll();
        return $this->render('administrateur/reclamation.html.twig', [
            'data' => $data,
        ]);

    }

    /**
     * @Route("/suppM/{id}", name="suppM")
     */
    public function suppM($id): Response
    {
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($id);
        $en =$this->getDoctrine()->getManager();
        $en->remove($mission);
        $en->flush();
        $this->addFlash('success','cette mission a bien été supprimé');
        return $this->redirectToRoute('listmission');

    }

    /**
     * @Route("/modifM/{id}", name="modifM")
     */
    public function modifM(Request $request,$id): Response
    {

        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($id);
        $form=$this->createForm(MissionType::class,$mission);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em= $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success','cette mission a bien été modifié');
            return $this->redirectToRoute('mission');

        }
        return $this->render('mission/index.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suppR/{id}", name="suppR")
     */
    public function suppR($id): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $en =$this->getDoctrine()->getManager();
        $en->remove($reclamation);
        $en->flush();
        $this->addFlash('success','cette reclamation a bien été supprimé');
        return $this->redirectToRoute('listreclamation');
    }




}
