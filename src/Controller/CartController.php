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
         // hna kanjib panier men session
    $cart = $session->get('cart', []);
    $cartWithData = [];
        // hna kanmchi 3la kol produit o kanjib les infos dyalou + la quantité

    foreach ($cart as $id => $quantity) {
        $cartWithData[] = [
            'product' => $this->productRepository->find($id),
            'quantity' => $quantity
        ];
    }

        // hna kan calculer total dial prix dial produits f panier

    $totalProduits = array_sum(array_map(function($item) {
        return $item['product']->getPrice() * $item['quantity'];
    }, $cartWithData));

        // ✅ debug bach nchouf contenu panier
    dump($cartWithData);
        // hna kanjib les villes men repository (peut-être bach livraison)

    $cities = $this->cityRepository->findAll();

    // hna kan afficher la vue cart/index.html.twig m3a data
    return $this->render('cart/index.html.twig', [
        'items' => $cartWithData,
        'totalProduits' => $totalProduits,
        'cities' => $cities
    ]);
}

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'] )]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        // hna kanjib panier men session
    $cart = $session->get('cart', []);

    if (!empty($cart[$id])) {
        $cart[$id]++;
    } else {
        $cart[$id] = 1;
    }

    $session->set('cart', $cart);

    // redirection vers panier bach tchouf résultat
    return $this->redirectToRoute('app_cart');
}

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods: ['GET'] )]
    public function decreaseFromCart(int $id, SessionInterface $session): Response
{   
     // hna kanjib panier men session
    $cart = $session->get('cart', []);
     // ila produit kayn, nqass men quantité
    if (!empty($cart[$id])) {
        $cart[$id]--; // nqass wa7ed
         // ila b9at 0, n7ayd produit
        if ($cart[$id] <= 0) {
            unset($cart[$id]); // si 0 -> supprimer
        }
    }
        // save panier updated
    $session->set('cart', $cart);

    return $this->redirectToRoute('app_cart');
}

    #[Route('/cart/remove/{id}', name: 'app_cart_product_remove', methods: ['GET'] )]
    public function removeToCart($id, SessionInterface $session):Response
    {
        // hna kanjib panier men session
        $cart = $session->get('cart', []);
            // ila produit kayn, n7aydou
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: 'app_cart_remove', methods: ['GET'] )]
    public function remove(SessionInterface $session):Response
    {
       // hna kansuprimi panier kaml (vider panier)
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
}
