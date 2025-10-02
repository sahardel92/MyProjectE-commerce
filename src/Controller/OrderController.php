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
        // 🛒 récupérer panier mn session
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        // 💰 calculer total dial produits
        $total = array_sum(array_map(function($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        // 🏙️ récupérer toutes les villes pour afficher f <select>
        $cities = $cityRepository->findAll();

        // 📋 création formulaire commande
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        return $this->render('order/index.html.twig', [
            'form'   => $form->createView(),
            'total'  => $total,
            'cities' => $cities, // 👉 pour le select
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'] )]
    public function cityShippingCost(City $city): JsonResponse
    {
        // 💸 récupérer frais livraison dial had la ville
        $cityShippingPrice = $city->getShippingCost();

        // ✅ retourner réponse JSON propre
        return new JsonResponse([
            'status'  => 200,
            'message' => 'ok',
            'content' => $cityShippingPrice
        ]);
    }
}
