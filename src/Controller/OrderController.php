<?php

namespace App\Controller;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\City;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = array_sum(array_map(function($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView(),  
            'total' => $total,
        ]);
    }

        #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'] )]
        public function cityShippingCost(City $city):Response
        {

            $cityShippingPrice = $city->getShippingCost();

            return new Response(json_encode(['status' => 200, 'message' => 'on', 'content' => $cityShippingPrice]));
            
        }
}
