<?php

namespace App\Controller;

use App\Entity\SousCategorie;
use App\Form\SousCategorieType;
use App\Repository\SousCategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SousCategorieController extends AbstractController
{
    /**
     * @Route("/sous/categorie", name="listSousCategorie")
     */
    public function index(SousCategorieRepository $sousCategorieRepository): Response
    {
        return $this->render('sous_categorie/index.html.twig', [
            'sous_categories' => $sousCategorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/Catnew", name="sous_categorie_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sousCategorie = new SousCategorie();
        $form = $this->createForm(SousCategorieType::class, $sousCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sousCategorie);
            $entityManager->flush();

            return $this->redirectToRoute('listSousCategorie');
        }

        return $this->render('sous_categorie/new.html.twig', [
            'sous_categorie' => $sousCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sousCat/{id}", name="sous_categorie_show")
     */
    public function show(SousCategorie $sousCategorie): Response
    {
        return $this->redirectToRoute('listSousCategorie');
    }

    /**
     * @Route("/sousCat/{id}/edit", name="sous_categorie_edit")
     */
    public function edit(Request $request, SousCategorie $sousCategorie, EntityManagerInterface $entityManager, $id): Response
    {
        $sc=$this->getDoctrine()->getRepository(SousCategorie::class)->find($id);

        $form = $this->createFormBuilder($sc)
            ->add('libelle')
            ->add('update',SubmitType::class)
            ->getForm() ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('listSousCategorie');
        }

        return $this->render('sous_categorie/edit.html.twig', [
            'sous_categorie' => $sousCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("sousCat/del/{id}", name="sous_categorie_deleteSCzz")
     */
    public function delete($id ,SousCategorieRepository $repository)
    {
        $sousCat = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($sousCat);
        $em->flush();
        return $this->redirectToRoute('listSousCategorie');
    }
}
