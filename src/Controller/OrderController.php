<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\City;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    // ✅ Route principale du checkout
    #[Route('/order', name: 'app_order', methods: ['GET','POST'])]
    public function index(
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository,
        CityRepository $cityRepository
    ): Response {
        // 🛒 Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        // 💰 Calcul du total
        $total = array_sum(array_map(fn($item) =>
            $item['product']->getPrice() * $item['quantity'], $cartWithData));

        // 🏙️ Liste des villes pour le select
        $cities = $cityRepository->findAll();

        // 🧾 Création du formulaire
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        // 🚦 Gestion du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Enregistre les infos de livraison
            $session->set('delivery_data', $form->getData());

            // ✅ Message de confirmation
            $this->addFlash('success', 'Adresse validée !');

            // ✅ Redirige vers la même page avec ?payment=1 pour afficher PayPal
            return $this->redirectToRoute('app_order', ['payment' => 1]);
        } elseif ($form->isSubmitted()) {
            // ⚠️ Si formulaire invalide
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
        }

        // 💳 Active le paiement si ?payment=1 est présent dans l’URL
        $showPayment = $request->query->get('payment') == 1;

        // 🖼️ Affiche la vue
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'items' => $cartWithData,
            'total' => $total,
            'cities' => $cities,
            'showPayment' => $showPayment,
        ]);
    }

    // ✅ API pour récupérer les frais de livraison selon la ville
    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'])]
    public function cityShippingCost(City $city): JsonResponse
    {
        return new JsonResponse([
            'status' => 200,
            'message' => 'ok',
            'content' => $city->getShippingCost()
        ]);
    }

    // ✅ Page de confirmation après paiement réussi
    #[Route('/order/confirm', name: 'app_order_confirm')]
    public function confirm(SessionInterface $session): Response
    {
        // 🧹 Vide le panier après paiement
        $session->set('cart', []);

        return $this->render('order/confirm.html.twig');
    }
}
