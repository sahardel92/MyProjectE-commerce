<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\City;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse; // 👉 important
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(
        Request $request, 
        SessionInterface $session, 
        ProductRepository $productRepository,
        CityRepository $cityRepository // 👉 pour récupérer les villes
    ): Response {
        //  hna kanjib panier men session
        $cart = $session->get('cart', []);
        $cartWithData = [];
        // hna kan7awl panier bach ndiro liste produits + quantité
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),// produit men DB
                'quantity' => $quantity  // quantité
            ];
        }

        // hna kan calculer total dial prix dial panier
        $total = array_sum(array_map(function($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        // hna kanjib toutes les villes (bach n'afficher f <select>
        $cities = $cityRepository->findAll();

        // création dial formulaire dial commande
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        //afficher la vue order/index.html.twig m3a data
        return $this->render('order/index.html.twig', [
            'form'   => $form->createView(),
            'items'  => $cartWithData,
            'total'  => $total,
            'cities' => $cities, // 👉 pour le select
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'] )]
    public function cityShippingCost(City $city): JsonResponse
    {
        // hna kanjib frais livraison dial had la ville
        $cityShippingPrice = $city->getShippingCost();

        // kanrja3 réponse JSON bach ajax yesta3melha
        return new JsonResponse([
            'status'  => 200,
            'message' => 'ok',
            'content' => $cityShippingPrice
        ]);
    }
    #[Route('/order/confirm', name: 'app_order_confirm')]
public function confirm(SessionInterface $session): Response
{
    //  vider panier men session ba3d paiement
    $session->set('cart', []);

    // afficher une page de confirmation
    return $this->render('order/confirm.html.twig');
}
}