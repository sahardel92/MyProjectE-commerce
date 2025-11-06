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
    #[Route('/order', name: 'app_order', methods: ['GET','POST'])]
    public function index(
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository,
        CityRepository $cityRepository
    ): Response {
        //  Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        //  Calcul du total
        $total = array_sum(array_map(
            fn($item) => $item['product']->getPrice() * $item['quantity'],
            $cartWithData
        ));

        //  Liste des villes pour le select
        $cities = $cityRepository->findAll();

        //  Création du formulaire
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        // 🧩 Vérification du formulaire
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // ✅ Formulaire valide → on enregistre les infos
                $session->set('delivery_data', $form->getData());

                $this->addFlash('success', '✅ Adresse validée ! Vous pouvez maintenant procéder au paiement.');

                // Redirection propre vers la page avec le paiement visible
                return $this->redirectToRoute('app_order', ['payment' => 1]);
            } else {
                // ❌ Formulaire invalide → message d’erreur
                $this->addFlash('error', '⚠️ Pour accéder au paiement, veuillez remplir tous les champs obligatoires.');
            }
        }


        //  Affiche le module PayPal uniquement après validation
        $showPayment = $request->query->get('payment') == 1;

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'items' => $cartWithData,
            'total' => $total,
            'cities' => $cities,
            'showPayment' => $showPayment,
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'])]
    public function cityShippingCost(City $city): JsonResponse
    {
        return new JsonResponse([
            'status' => 200,
            'message' => 'ok',
            'content' => $city->getShippingCost(),
        ]);
    }

    #[Route('/order/confirm', name: 'app_order_confirm')]
    public function confirm(SessionInterface $session): Response
    {
        // 🧹 Vide le panier après paiement
        $session->set('cart', []);

        return $this->render('order/confirm.html.twig');
    }
}
