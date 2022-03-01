<?php

namespace App\Controller;

use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(PanierRepository $repository , ProduitRepository $produitRepository): Response
    {
        $utilisateur = $this->getUser();
        $panier = $repository->findBy(['utilisateur' => $utilisateur->getId()])[0];
        $pr = $panier->getProduits()->toArray();
        $qt = 0;
        $prixTot=7;
        foreach ($panier->getProduits()->toArray() as $p) {

            $pr = $produitRepository->find($p->getId());
            $prixTot = $prixTot + ($pr->getPrix()*$pr->getQuantite());
            $qt = $qt + $pr->getQuantite();
        }


        Stripe::setApiKey("rk_test_51KUvLNIc0oubFheRqSoj1IUDvQ2Lhj8lyTXj6KE533bJlUAFlTVZZYr18kDBDKm76cnuPJbMZftUZgZ07DGNOyvD00ZRxbv4zd");
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        "name" => "Montant Totale",
                    ]
                    ,
                    'unit_amount' =>$prixTot*100,
                ],

                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('succes_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($session->url , 303);
    }

    /**
     * @Route("/succes_url", name="succes_url")
     */
    public function succesurl(): Response
    {
        return $this->redirectToRoute('panierToCommande');
    }

    /**
     * @Route("/cancel_url", name="cancel_url")
     */
    public function cancelurl(): Response
    {
        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/paymentMethod", name="paymentMethod")
     */
    public function paymentMethod(PanierRepository $repository): Response
    {
        $total=0;
        $data=[];
        $sum=0;
        $utilisateur = $this->getUser();
        // $this->addFlash();
        $d = $repository->findBy(['utilisateur'=>$utilisateur->getId()])[0];
        $sum = $d->getProduits()->count();
        $data = $d->getProduits()->toArray();
        foreach ($data as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        return $this->render('panier/payment.html.twig',[
            'data' => $data , 'sumP'=>$sum , 'total'=>$total
        ]);
    }
}
