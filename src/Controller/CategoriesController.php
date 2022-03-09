<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use App\Form\CategoriesType;
use App\Form\ProductsType;
use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class CategoriesController extends AbstractController
{
    /**
     * @param CategorieRepository $repository
     * @return Response
     * @Route("/administrateur/categorie",name="listcategorie")
     */

    public function afficheCat(CategorieRepository $repository , PaginatorInterface $paginator , Request $request)
    {

        $categorie = $repository->findAll();
        $categories = $paginator->paginate(
            $categorie,
            $request->query->getInt('page',1),
            4
        );

        return $this->render('administrateur/categorie.html.twig',
            ['categories' => $categories , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
    }

    /**
     * @Route("/deleteC/{id}",name="deletecategorie")
     */
    function deleteC($id, CategorieRepository $repository)
    {
        $categorie = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('listcategorie');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/Categorie/add", name="addcategorie")
     */
    function add(Request $request) {
        $categorie=new Categorie();
        $form=$this->createForm(CategoriesType::class, $categorie);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() ) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($categorie);
            foreach ($request->request->get('categories')['sousCategories'] as $sousCategoryID){
                $scat = $this->getDoctrine()->getRepository(SousCategorie::class)->find($sousCategoryID);
                $scat->setCategorie($categorie);
            }
            $em->flush();
            return $this->redirectToRoute('listcategorie');
        }
        return $this->render('categories/add.html.twig',[
            'form'=>$form->createView(), 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
        ]);

    }


    /**
     * @Route("Categorie/update/{id}" , name="updatecategorie")
     */
    function Update(CategorieRepository $repository,$id,Request $request) {
        $categorie=$repository->find($id);
        $form=$this->createForm(CategoriesType::class,$categorie);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("listcategorie");
        }
        return $this->render('categories/update.html.twig',
            [
                'form'=>$form->createView(),'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()
            ]);

    }



}

