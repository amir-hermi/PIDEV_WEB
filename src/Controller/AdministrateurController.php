<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

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
    public function listClient(): Response
    {
        return $this->render('administrateur/client.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
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

    /**
     * @return Response
     * @Route ("/dashboard", name="dashboard")
     */

    public function indexAction(ProduitRepository $repository)
    {
        $nikeCount = $repository->countNike();
        $adidasCount = $repository->countAdidas();
        $pumaCount = $repository->countPuma();
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Marques', 'Product per Mark'],
                ['Nike',    (int) $nikeCount],
                ['Adidas',    (int) $adidasCount],
                ['Puma',  (int) $pumaCount],

            ]
        );
        $pieChart->getOptions()->setTitle('Your Products List');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render('administrateur/dashboard.html.twig', array('piechart' => $pieChart));
    }
}
