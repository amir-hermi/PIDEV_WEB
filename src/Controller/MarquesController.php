<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Marque;
use App\Entity\Produit;
use App\Form\MarquesType;
use App\Form\ProductsType;
use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MarquesController extends AbstractController
{

    /**
     * @param MarqueRepository $repository
     * @return Response
     * @Route("/administrateur/marque",name="listMarques")
     */

    public function afficheM (MarqueRepository $repository , PaginatorInterface $paginator , Request $request){
        //$repo=$this->getDoctrine()->getRepository(Produit::class);
        $marque=$repository->findAll();
        $marques = $paginator->paginate(
            $marque,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('administrateur/marque.html.twig',
            ['marques'=>$marques , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
    }

    /**
     * @Route("/deleteM/{id}",name="deletemarque")
     */
    function deleteM($id, MarqueRepository $repository)
    {
        $marque = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($marque);
        $em->flush();
        return $this->redirectToRoute('listMarques');

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/Marques/add", name="addmarque")
     */
    function add(Request $request) {
        $marque=new Marque();
        $form=$this->createForm(MarquesType::class, $marque);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($marque);
            $em->flush();
            return $this->redirectToRoute('listMarques');
        }
        return $this->render('marques/add.html.twig',[
            'form'=>$form->createView(),'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);
    }

    /**
     * @Route("Marque/update/{id}" , name="updatemarque")
     */
    function Update(MarqueRepository $repository,$id,Request $request) {
        $marque=$repository->find($id);
        $form=$this->createForm(MarquesType::class,$marque);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("listMarques");
        }
        return $this->render('marques/update.html.twig',
            [
                'form'=>$form->createView(),'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
            ]);

    }





}
