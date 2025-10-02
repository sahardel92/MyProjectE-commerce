<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CityRepository $cityRepository
    ) {}

   #[Route('/cart', name: 'app_cart', methods: ['GET'] )]
    public function index(SessionInterface $session): Response
    {
    $cart = $session->get('cart', []);
    $cartWithData = [];

    foreach ($cart as $id => $quantity) {
        $cartWithData[] = [
            'product' => $this->productRepository->find($id),
            'quantity' => $quantity
        ];
    }

    // Calcul total
    $totalProduits = array_sum(array_map(function($item) {
        return $item['product']->getPrice() * $item['quantity'];
    }, $cartWithData));

    // ✅ Debug ici
    dump($cartWithData);

    $cities = $this->cityRepository->findAll();

    return $this->render('cart/index.html.twig', [
        'items' => $cartWithData,
        'totalProduits' => $totalProduits,
        'cities' => $cities
    ]);
}

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'] )]
    public function addToCart(int $id, SessionInterface $session): Response
    {
    $cart = $session->get('cart', []);

    if (!empty($cart[$id])) {
        $cart[$id]++;
    } else {
        $cart[$id] = 1;
    }

    $session->set('cart', $cart);

    // Rediriger vers la page panier
    return $this->redirectToRoute('app_cart');
}

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods: ['GET'] )]
    public function decreaseFromCart(int $id, SessionInterface $session): Response
{
    $cart = $session->get('cart', []);
    if (!empty($cart[$id])) {
        $cart[$id]--; // --
        if ($cart[$id] <= 0) {
            unset($cart[$id]); // si 0 -> supprimer
        }
    }
    $session->set('cart', $cart);

    return $this->redirectToRoute('app_cart');
}

    #[Route('/cart/remove/{id}', name: 'app_cart_product_remove', methods: ['GET'] )]
    public function removeToCart($id, SessionInterface $session):Response
    {
        $cart = $session->get('cart', []);
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: 'app_cart_remove', methods: ['GET'] )]
    public function remove(SessionInterface $session):Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
}
