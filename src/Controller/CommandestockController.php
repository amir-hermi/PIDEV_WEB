<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Commandestock;
use App\Form\CommandestockType;
use App\Repository\CommandestockRepository;
use App\Repository\FournisseurRepository;
use App\Repository\ListstockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandestockController extends AbstractController
{
    /**
     * @Route("/commandestock", name="commandestock")
     */
    public function index(): Response
    {
        $commandestock = $this->getDoctrine()->getRepository(Commandestock::class)->findAll();
        return $this->render('commandestock/commandestock.html.twig', [
            'commandestock' => $commandestock,'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);
    }
    /**
     * @Route("commandestock/supp/{id}", name="suppCS")
     */
    public function delete(CommandestockRepository $repository,$id): Response
    {
        $liststock = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($liststock);
        $em->flush();
        $this->addFlash(
            'info',
            'Deleted successfully!'
        );
        return $this->redirectToRoute('commandestock');
    }

    /**
     * @Route ("/commandeaccepter/{id}", name="commandeaccepter")
     */
    public function accepter (CommandestockRepository  $repository , $id): Response
    {
        $commandestock =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $commandestock->setEtat('Accepter');

        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('commandestock');
    }
    /**
     * @Route ("/commanderefuser/{id}", name="commanderefuser")
     */
    public function refuser (CommandestockRepository $repository , $id): Response
    {
        $commandestock =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $commandestock->setEtat('Refuser');

        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('commandestock');
    }

    /**
     * @param CommandestockRepository $repository
     * @return Response
     * @Route ("/commandestock/commandestockdate", name="triParDate")
     */
    function AfficheByDate(CommandestockRepository  $repository){
        $commandestock=$repository->OrderByDatevalid();
        return $this->render("commandestock/commandestock.html.twig",
           ['commandestock'=>$commandestock , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
        //return $this->redirectToRoute('commandestock',['commandes'=>$commandestock]);
        //valide
    }

    /**
     * @param CommandestockRepository $repository
     * @return Response
     * @Route("commandestock/listdqll")
     */
    function OrderByMail(CommandestockRepository $repository){
       $commandestock=$repository->orderByMail();
       return $this->render('commandestock/commandestock.html.twig',['commandestock' => $commandestock]);

    }

    /**
     * @param CommandestockRepository $repository
     * @param Request $request
     * @return Response
     * @Route ("commandestock/recherche",name="recherche")
     */
    function recherche(CommandestockRepository $repository,Request $request){
      //en marche
        $data=$request->get('search');
        $commandestock=$repository->findBy(['id'=>$data]);
        return $this->render('commandestock/commandestock.html.twig', [
            'commandestock' => $commandestock,'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);
    }
    /**
     * @param CommandestockRepository $repository
     * @param Request $request
     * @return Response
     * @Route ("commandestock/recherche",name="recherche")
     */
    function searchparFournisseur(CommandestockRepository $repository,Request $request)
    {
        //en marche
        $data = $request->get('search');
        $commandestock = $repository->findBy(['fournisseur' => $data]);
        return $this->render('commandestock/commandestock.html.twig', [
            'commandestock' => $commandestock,'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);
    }
        /**
         * @param CommandestockRepository $repository
         * @param Request $request
         * @return Response
         * @Route ("commandestock/rechercher",name="recherche")
         */
        function searchparQuantite(CommandestockRepository $repository,Request $request){
            //en marche
            $data=$request->get('search');
            $commandestock=$repository->findBy(['quantite'=>$data]);
            return $this->render('commandestock/commandestock.html.twig', [
                'commandestock' => $commandestock,'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
            ]);
    }

    /**
     * @param CommandestockRepository $repository
     * @return Response
     * @Route ("/commandestock/commandestockquantite")
     */
    function AfficheByquantite(CommandestockRepository  $repository){
        $commandestockk=$repository->OrderByQuantite();
        return $this->render("commandestock/commandestock.html.twig",
            ['commandestock'=>$commandestockk]);
        //notvalide
    }







}
