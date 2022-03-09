<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Fournisseur;
use App\Entity\Utilisateur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\core\Type\FileType;

class FournisseurController extends AbstractController
{
    /**
     * @Route("/fournisseur", name="fournisseur")
     */
    public function index(): Response
    {
        return $this->render('fournisseur/index.html.twig', [
            'controller_name' => 'FournisseurController',
        ]);
    }

    /**
     * @Route("administrateur/dF/{id}//{username}", name="dF")
     */
    public function dF($id , $username): Response
    {
        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findBy(["username"=>$username]);
        $fournisseur = $this->getDoctrine()->getRepository(Fournisseur::class)->find($id);
        $en = $this->getDoctrine()->getManager();
        $en->remove($fournisseur);
        $en->remove($utilisateur[0]);
        $en->flush();
        $this->addFlash(
            'info',
            'Deleted successfully!'
        );
        return $this->redirectToRoute('affichefournisseur');
    }

    /**
     * @Route("administrateur/Affiche",name="affichefournisseur")
     */
    public function affiche(): Response
    {
        //récupérer le repository
        $repo = $this->getDoctrine()->getRepository(Fournisseur::class);
        $FOURNISSEUR = $repo->findAll();
        return $this->render('fournisseur/Affiche.html.twig', ['fournisseur' => $FOURNISSEUR , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("administrateur/fournisseur/Add",name="addF")
     */
    public function Add(Request $request , UserPasswordEncoderInterface $passwordEncoder)
    {
        $fournisseur = new Fournisseur();
        $utilisateur = new Utilisateur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        //récupérer les données saisies
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $fournisseur->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ...handle exeption if something happens during
            }
            //Action d'ajout
            $em = $this->getDoctrine()->getManager();

            $fournisseur->setMDPFournisseur($passwordEncoder->encodePassword($utilisateur, $fournisseur->getMDPFournisseur()));
            $fournisseur->setImage($fileName);

            $em->persist($fournisseur);
            $em->flush();
            $utilisateur->setUsername($fournisseur->getNomFournisseur());
            $utilisateur->setRoles(["ROLE_FOURNISSEUR"]);
            $utilisateur->setEmail($fournisseur->getAdresseFournisseur());
            $utilisateur->setEtat("Debloquer");
            $utilisateur->setLastname($fournisseur->getLastname());
            $utilisateur->setTel($fournisseur->getTel());
            $utilisateur->setPassword($fournisseur->getMDPFournisseur());
            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash(
                'info',
                'Added successfully!'
            );
            return $this->redirectToRoute('affichefournisseur');
        }
        return $this->render('fournisseur/Add.html.twig', ['formfournisseur' => $form->createView() , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
    }

    /**
     * @Route ("administrateur/fournisseur/UpdateF/{id}/{username}",name="updateF")
     */
    public function update( UserPasswordEncoderInterface $passwordEncoder,FournisseurRepository $repository, $username,$id, Request $request)
    {
        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findBy(["username"=>$username]);
        $fournisseur = $repository->find($id);
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->add('Update', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $fournisseur->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ...handle exeption if something happens during
            }
            //Action d'ajout
            $em = $this->getDoctrine()->getManager();
            $fournisseur->setMDPFournisseur($passwordEncoder->encodePassword($utilisateur, $fournisseur->getMDPFournisseur()));
            $fournisseur->setImage($fileName);
            $utilisateur[0]->setUsername($fournisseur->getNomFournisseur());
            $utilisateur[0]->setRoles(["ROLE_FOURNISSEUR"]);
            $utilisateur[0]->setEmail($fournisseur->getAdresseFournisseur());
            $utilisateur[0]->setEtat("Debloquer");
            $utilisateur[0]->setLastname($fournisseur->getLastname());
            $utilisateur[0]->setTel($fournisseur->getTel());
            $utilisateur[0]->setPassword($fournisseur->getMDPFournisseur());
            $em->flush();

            $this->addFlash(
                'info',
                'updated successfully!'
            );
            return $this->redirectToRoute('affichefournisseur');
        }
        return $this->render('fournisseur/Update.html.twig', ['f' => $form->createView() , 'CommandeNonlue'=>$this->getDoctrine()->getRepository(Commande::class)->commandeAdmin()]);
    }

    /**
     * @param FournisseurRepository $fournisseurRepository
     * @return Response
     * @Route("fournisseur/ListDQL")
     */
    function OrderByMailDQL(FournisseurRepository $fournisseurRepository)
    {
        $fournisseur = $fournisseurRepository->OrderByMail();
        return $this->render('fournisseur/Affiche.html.twig', ['fournisseur' => $fournisseur]);

    }

    /**
     * @param FournisseurRepository $fournisseurRepository
     * @return Response
     * @Route("fournisseur/idDQL")
     */
    function OrderByidDQL(FournisseurRepository $fournisseurRepository)
    {
        $fournisseur = $fournisseurRepository->OrderByid();
        return $this->render('fournisseur/Affiche.html.twig', ['fournisseur' => $fournisseur]);

    }


}
