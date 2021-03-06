<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Mission;
use App\Entity\Panier;
use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Form\MissionType;
use App\Form\UtilisateurType;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\MissionRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UtilisateurRepository;
use App\Security\LogInFormAthenticator;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Constraints\DateTime;

class AdministrateurController extends AbstractController
{
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function index(): Response
    {
        $utilisateur = $this->getUser();
        return $this->render('administrateur/dashboard.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

    /**
     * @Route("/administrateur/commande", name="listcommande")
     */
    public function listCommande(Request $request, PaginatorInterface $paginator, CommandeRepository $repository): Response
    {

        $data = $repository->findAll();
        if($request->isMethod('post')){
            $datawithrecherche = $request->get("recherche");
            $data = $repository->rechercheParRef($datawithrecherche);
        }
        $commandes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),//num page
            4
        );


        return $this->render('administrateur/commande.html.twig', [
            'data' => $commandes,
        ]);
    }
    /******************************************************************************************************
     * Client
     ************************************************************************************************/
    /*
        /**
         * @Route("/register", name="app_register")
         */
    /*
   public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UsersAuthenticator $authenticator): Response
   {
       $user = new Users();
       $form = $this->createForm(UtilisateurType::class, $user);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           // encode the plain password
           $user->setPassword(
               $passwordEncoder->encodePassword(
                   $user,
                   $form->get('plainPassword')->getData()
               )
           );
           // On g??n??re un token et on l'enregistre
           $user->setActivationToken(md5(uniqid()));

           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($user);
           $entityManager->flush();

           // do anything else you need here, like send an email

           return $guardHandler->authenticateUserAndHandleSuccess(
               $user,
               $request,
               $authenticator,
               'main' // firewall name in security.yaml
           );
       }

       return $this->render('registration/register.html.twig', [
           'registrationForm' => $form->createView(),
       ]);
   }

    */

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, Session $session, GuardAuthenticatorHandler $guardHandler, LogInFormAthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        //test de s??curit??, un utilisateur connect?? ne peut pas s'inscrire
        $utilisateur = $this->getUser();
        if ($utilisateur) {
            $session->set("message", "Vous ne pouvez pas cr??er un compte lorsque vous ??tes connect??");
            return $this->redirectToRoute('membre');
        }

        $utilisateur = new Utilisateur();
        $panier = new Panier();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            $role = ['ROLE_USER'];
            $utilisateur->setRoles($role);
            $utilisateur->setEtat('Bloquer');
            // genere le token

            $utilisateur->setActivationToken(md5(uniqid()));

            $entityManager->persist($utilisateur);
            $panier->setUtilisateur($utilisateur);

            $entityManager->persist($panier);
            $utilisateur->setPanier($panier);
            $entityManager->flush();
            $message = (new \Swift_Message('Activation Nouveau compte'))
                // On attribue l'exp??diteur

                ->setFrom('testutilisateurs1@gmail.com')
                // On attribue le destinataire

                ->setTo($utilisateur->getEmail())
                // On cr??e le texte avec la vue

                ->setBody($this->renderView(
                    'email/activation.html.twig', ['token' => $utilisateur->getActivationToken()]
                ),
                    'text/html'

                );
            $mailer->send($message);
            return $this->redirectToRoute('utilisateur_index');
            //$mailer->send($message);


            //return $this->redirectToRoute('app_login');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
            'cat' => $categorie
        ]);


    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UtilisateurRepository $utilisateur)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de donn??es
        $utilisateur = $utilisateur->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associ?? ?? ce token
        if (!$utilisateur) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $utilisateur->setActivationToken("");
        $utilisateur->setEtat("Debloquer");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // On g??n??re un message
        $this->addFlash('message', 'Utilisateur activ?? avec succ??s');

        // On retourne ?? l'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/administrateur/client", name="listclient")
     */
    public function listClient(UtilisateurRepository $repository): Response
    {
        $data = $repository->findAll();
        $da = [];
        foreach ($data as $d) {
            if (in_array('ROLE_USER', $d->getRoles())) {
                array_push($da, $d);
            }
        }
        return $this->render('administrateur/client.html.twig', [
            'data' => $da,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur, UserPasswordEncoderInterface $passwordEncoder, Session $session, $id): Response
    {
        $utilisateur = $this->getUser();
        if ($utilisateur->getId() != $id) {
            // un utilisateur ne peut pas en modifier un autre
            $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
            return $this->redirectToRoute('membre');
        }
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/editProfile.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur, Session $session, $id): Response
    {
        $utilisateur = $this->getUser();
        if ($utilisateur->getId() != $id) {
            // un utilisateur ne peut pas en supprimer un autre
            $session->set("message", "Vous ne pouvez pas supprimer cet utilisateur");
            return $this->redirectToRoute('membre');
        }

        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            // permet de fermer la session utilisateur et d'??viter que l'EntityProvider ne trouve pas la session
            $session = new Session();
            $session->invalidate();
        }

        return $this->redirectToRoute('home');
    }
    /*
        /**
         * @Route("/administrateurl/clientl", name="listclientl")
         */
    /*
    public function listClientl(UtilisateurRepository $repository): Response
    {
        $data = $repository->findAll();
        $da =[];
        foreach ($data as $d){
            if( in_array('ROLE_USER', $d->getRoles())){
                array_push($da , $d);
            }
        }
        return $this->render('baseAdmin.html.twig', [
            'data' => $da,
        ]);
    }


    */

    /**
     * @Route("/updateClient{idP}", name="updateprofile")
     */
    public function update($idP, UtilisateurRepository $repository, Request $request): Response
    {
        $client = $repository->find($idP);

        $data = $repository->findAll();
        $da = [];
        foreach ($data as $d) {
            if (in_array('ROLE_USER', $d->getRoles())) {
                array_push($da, $d);
            }
        }
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //$client->setPassword($client->encodePassword($client, $client->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('utilisateur/editProfile.html.twig', [
            'form' => $form->createView(),
            'data' => $da,
        ]);
    }

    /**
     * @Route("/administrateur/updateClient{idP}", name="updateclient")
     */
    public function updateClient($idP, UtilisateurRepository $repository, Request $request): Response
    {
        $client = $repository->find($idP);

        $data = $repository->findAll();
        $da = [];
        foreach ($data as $d) {
            if (in_array('ROLE_USER', $d->getRoles())) {
                array_push($da, $d);
            }
        }
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $client->setPassword($client->encodePassword($client, $client->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listclient');
        }
        return $this->render('administrateur/updateClient.html.twig', [
            'form' => $form->createView(),
            'data' => $da,
        ]);
    }

    /**
     * @Route("/administrateur/deletClient{idP}", name="deletClient")
     */
    public function suppc($idP, UtilisateurRepository $repository): Response
    {
        $client = $repository->find($idP);
        $en = $this->getDoctrine()->getManager();
        $en->remove($client);
        $en->flush();
        return $this->redirectToRoute('listclient');
    }


    /**
     * @Route ("/clientbloque/{id}", name="clientbloque")
     */
    public function bloque(UtilisateurRepository $repository, $id): Response
    {
        $client = $repository->find($id);
        $manager = $this->getDoctrine()->getManager();
        $client->setEtat('Bloquer');


        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listclient');
    }

    /**
     * @Route ("/clientdebloque/{id}", name="clientdebbloque")
     */
    public function debloque(UtilisateurRepository $repository, $id): Response
    {
        $client = $repository->find($id);
        $manager = $this->getDoctrine()->getManager();
        $client->setEtat('Debloquer');


        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listclient');
    }

    /****************************************************************************************************************
     * livreur update tafsiikh
     ***************************************************************************************************************/
    /**
     * @Route("/administrateur/livreur", name="listLivreur")
     */
    public function listLivreur(UtilisateurRepository $repository): Response
    {
        $data = $repository->findAll();
        $da = [];
        foreach ($data as $d) {
            if (in_array('ROLE_LIVREUR', $d->getRoles())) {
                array_push($da, $d);
            }
        }
        return $this->render('administrateur/livreur.html.twig', [
            'data' => $da,
        ]);
    }

    /**
     * @Route("/administrateur/deletlivreur{idP}", name="deletlivreur")
     */
    public function suppLivreur($idP, UtilisateurRepository $repository): Response
    {
        $client = $repository->find($idP);
        $en = $this->getDoctrine()->getManager();
        $en->remove($client);
        $en->flush();
        return $this->redirectToRoute('listLivreur');
    }


    /**
     * @Route("/newlivreur", name="livreur_new", methods={"GET","POST"})
     */
    public function newl(Request $request, UserPasswordEncoderInterface $passwordEncoder, Session $session): Response
    {

        $utilisateur = new Utilisateur();
        $panier = new Panier();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            $role = ['ROLE_LIVREUR'];
            $utilisateur->setRoles($role);

            $entityManager->persist($utilisateur);
            $utilisateur->setActivationToken("");
            $panier->setUtilisateur($utilisateur);
            $entityManager->persist($panier);
            $utilisateur->setPanier($panier);
            $entityManager->flush();

            return $this->redirectToRoute('listLivreur');
        }

        return $this->render('administrateur/newLivreur.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/administrateur/updatelivreur{idP}", name="updatelivreur")
     */
    public function updatelivreur($idP, UtilisateurRepository $repository, UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        $fournisseur = $repository->find($idP);
        $data = $repository->findAll();

        $form = $this->createForm(ClientType::class, $fournisseur);
        $form->handleRequest($request);;

        if ($form->isSubmitted()) {
            $fournisseur->setPassword($passwordEncoder->encodePassword($fournisseur, $fournisseur->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listLivreur');
        }
        return $this->render('administrateur/updatelivreur.html.twig', [
            'form' => $form->createView(),

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
     * @Route("/administrateur/reclamation/pdfR/{id}", name="pdfR")
     */
    public function pdfR(ReclamationRepository $repository, $id): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $data = $repository->find($id);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrateur/pdfR.html.twig', [
            'data' => $data,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        exit(0);
    }

    /**
     * @Route("/administrateur/mission/pdfM/{id}", name="pdfM")
     */
    public function pdfM(MissionRepository $repository, $id): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $data = $repository->find($id);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('administrateur/pdfM.html.twig', [
            'data' => $data,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        exit(0);
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
        $en = $this->getDoctrine()->getManager();
        $en->remove($mission);
        $en->flush();
        $this->addFlash('success', 'cette mission a bien ??t?? supprim??');
        return $this->redirectToRoute('listmission');

    }

    /**
     * @Route("/modifM/{id}", name="modifM")
     */
    public function modifM(Request $request, $id): Response
    {

        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($id);
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'cette mission a bien ??t?? modifi??');
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
        $en = $this->getDoctrine()->getManager();
        $en->remove($reclamation);
        $en->flush();
        $this->addFlash('success', 'cette reclamation a bien ??t?? supprim??');
        return $this->redirectToRoute('listreclamation');
    }





    /****************************************************************************************************************
     * FOURNISSEUR mazel tafsikh
     ***************************************************************************************************************/
    /**
     * @Route("/administrateur/fournisseur", name="listFournisseur")
     */
    public function listFournisseur(UtilisateurRepository $repository): Response
    {
        $data = $repository->findAll();
        $da = [];
        foreach ($data as $d) {
            if (in_array('ROLE_FOURNISSEUR', $d->getRoles())) {
                array_push($da, $d);
            }
        }
        return $this->render('administrateur/fournisseur.html.twig', [
            'data' => $da,
        ]);
    }

    /**
     * @Route("/newf", name="fournisseur_new", methods={"GET","POST"})
     */
    public function newf(Request $request, UserPasswordEncoderInterface $passwordEncoder, Session $session): Response
    {

        $utilisateur = new Utilisateur();
        $panier = new Panier();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            $role = ['ROLE_FOURNISSEUR'];
            $utilisateur->setRoles($role);
            $utilisateur->setActivationToken("");
            $entityManager->persist($utilisateur);
            $panier->setUtilisateur($utilisateur);
            $entityManager->persist($panier);
            $utilisateur->setPanier($panier);
            $entityManager->flush();

            return $this->redirectToRoute('listFournisseur');

        }

        return $this->render('administrateur/newfournisseur.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/administrateur/updatefournisseur{idP}", name="updatefournisseur")
     */
    public function updatefournisseur($idP, UtilisateurRepository $repository, UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        $fournisseur = $repository->find($idP);
        $data = $repository->findAll();

        $form = $this->createForm(ClientType::class, $fournisseur);
        $form->handleRequest($request);;

        if ($form->isSubmitted()) {
            $fournisseur->setPassword($passwordEncoder->encodePassword($fournisseur, $fournisseur->getPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listFournisseur');
        }
        return $this->render('administrateur/updateFournisseur.html.twig', [
            'form' => $form->createView(),

        ]);
    }
    /****************************************************************************************************************
     *Clien
     ********************************************************************************************************************/


    /****************************************************************************************************************
     * Produit ,commande
     ***************************************************************************************************************/


    /**
     * @Route("/administrateur/produit", name="listproduit")
     */
    public function listProduit(): Response
    {
        return $this->render('administrateur/produit.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }


    /**
     * @Route("/administrateur/updateCommande{idP}", name="updateCommande")
     */
    public function updateCommande($idP, CommandeRepository $repository, Request $request): Response
    {
        $comm = $repository->find($idP);
        $newvalue = $request->query->get("value");
        $n = str_replace("_"," ",$newvalue);
        $comm->setStatus($n);
        /* $form = $this->createFormBuilder($comm)
             ->add('status',ChoiceType::class,[
                 'placeholder' => null,
                 'attr'=>array('style'=>'width:15px  ; border-radius: 50px;',  'customattr'=>'customdata'),
                 'choices'  => [
                     'En Attente' => 'En attente',
                     'Annul??e' => 'Annul??e',
                     'Confirm??' => 'Confirm??e',
                     'En cours de preparation' => 'En cours de preparation',
                     'Livraison en cours' => 'Livraison en cours',
                     'Livr??e' => 'Livr??e',
                 ]])
             ->add('Confirmer',SubmitType::class)
             ->getForm();
         $form->handleRequest($request);*/

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $this->addFlash('updateStatus', 'Commande modifier avec succ??s');
        return $this->redirectToRoute('listcommande');
    }



    /************** Statistique Commande ****************/

    /**
     * @Route("/dashboard" , name="dashboard")
     */
    public function dashboard( ProduitRepository $produitRepository,CommandeRepository $repository)
    {
        //************************** Hassen ********************
       $nikeCount = $produitRepository->countNike();
        $adidasCount = $produitRepository->countAdidas();
        $pumaCount = $produitRepository->countPuma();
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Marques', 'Product per Mark'],
                ['Nike',    (int) $nikeCount],
                ['Adidas',    (int) $adidasCount],
                ['Puma',  (int) $pumaCount],

            ]
        );
        $pieChart->getOptions()->setTitle('Your Products List');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(550);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

//**************************** Amir ***************************
        $newCommandeCount =$repository->findNewCommande();
        $commandeTrite = $repository->findCommandesTrit??e();
        $commandeNonTrite = $repository->findCommandesNonTrit??e();
        $AmirChart1 = new PieChart();
        $AmirChart1->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['Commande Trit??e',((int) $commandeTrite)],
                ['Commande non Trit??e',((int) $commandeNonTrite)],
            ]
        );
        $AmirChart1->getOptions()->setTitle("L'ETAT DES COMMANDES D'AUJOURD'HUIT");
        $AmirChart1->getOptions()->setHeight(400);
        $AmirChart1->getOptions()->setIs3D(2);
        $AmirChart1->getOptions()->setWidth(550);
        $AmirChart1->getOptions()->getTitleTextStyle()->setBold(true);
        $AmirChart1->getOptions()->getTitleTextStyle()->setColor('#009900');
        $AmirChart1->getOptions()->getTitleTextStyle()->setItalic(true);
        $AmirChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $AmirChart1->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $this->render('administrateur/dashboard.html.twig', array('piechart' => $pieChart,'AmirChart1' => $AmirChart1 , 'newCommandeCount'=>$newCommandeCount));
    }

    /**
     * @Route("/administrateur/categorie", name="listcategorie")
     */


    public function afficheCat (CategorieRepository $repository){
        //$repo=$this->getDoctrine()->getRepository(Produit::class);
        $categorie=$repository->findAll();
        return $this->render('administrateur/categorie.html.twig',
            ['categorie'=>$categorie]);

    }

}
