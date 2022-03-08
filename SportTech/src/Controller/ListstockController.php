<?php

namespace App\Controller;

use App\Entity\Commandestock;
use App\Entity\Liststock;
use App\Form\CommandestockType;

use App\Repository\CommandestockRepository;
use App\Repository\FournisseurRepository;
use App\Repository\ListstockRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListstockController extends AbstractController
{

    /**
     * @Route("administrateur/supp/{id}", name="d")
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
     * @Route("administrateur/Afficheliststock",name="afficheliststock")
     */
    public function afficheliststock(): Response
    {
        //récupérer le repository
        $repo=$this->getDoctrine()->getRepository(Commandestock::class);
        $LISTSTOCK=$repo->findAll();




        return $this->render('liststock/Affiche.html.twig', ['liststock' => $LISTSTOCK]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("administrateur/liststock/Add",name="add")
     */
    public function Add(Request $request , ProduitRepository $produitRepository){
        $liststock=new Commandestock();
        $form=$this->createForm(CommandestockType::class,$liststock);
        //récupérer les données saisies
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            //Action d'ajout
            $liststock->setEtat('en attende');
            $em=$this->getDoctrine()->getManager();
            $liststock->setDate(date_create());
            $em->persist($liststock);
            foreach ($request->request->get("commandestock")["produit"] as $produitId){
                $produit = $produitRepository->find($produitId);
                $liststock->addProduit($produit);
                $em->flush();
                $this->addFlash(
                    'info',
                    'Added successfully!'
                );
            }
            return $this->redirectToRoute('afficheliststock');
        }
        return $this->render('liststock/Add.html.twig', ['formliststock' =>$form->createView()]);
    }
    /**
     * @Route ("administrateur/liststock/Update/{id}",name="update")
     */
    public function update(CommandestockRepository  $repository,$id,Request $request){
        $liststock=$repository->find($id);
        $form=$this->createForm(CommandestockType::class,$liststock);
        $form->add('Update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //Action d'ajout
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash(
                'info',
                'updated successfully!'
            );
            return $this->redirectToRoute('afficheliststock');
        }
        return $this->render('liststock/Update.html.twig', ['f' =>$form->createView()]);
    }




}
