<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Review;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'] )]
    public function index(
        ProductRepository $productRepository, 
        CategoryRepository $categoryRepository, 
        Request $request, 
        PaginatorInterface $paginator
    ): Response {
         // hna kanjib tous les produits men DB triés b id DESC (jdidin lwlin)
        $data = $productRepository->findBy([], ['id' => 'DESC']);
        // hna kandir pagination : 10 produits par page
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );
        // hna kan afficher page home/index.html.twig m3a les produits et categories
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/home/product/{id}/show', name: 'app_home_product_show', methods: ['GET'] )]
    public function show(
        Product $product, 
        ProductRepository $productRepository, 
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    ): Response {
        // hna kanjib 5 produits jdadin bach n'afficher "dernier produits"
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], 5);

        // 🔥 hna kanjib reviews (avis) li 3and had produit, triés par date DESC
        $reviews = $em->getRepository(Review::class)->findBy(
            ['product' => $product],
            ['createdAt' => 'DESC']
        );
         // hna kan afficher show.html.twig m3a produit + dernier produits + categories + avis
        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProducts,
            'categories' => $categoryRepository->findAll(),
            'reviews' => $reviews, 
        ]);
    }

    #[Route('/home/product/subcategory/{id}/filter', name: 'app_home_product_filter', methods: ['GET'] )]
    public function filter(
        $id, 
        SubCategoryRepository $subCategoryRepository, 
        CategoryRepository $categoryRepository
    ): Response {
        // hna kanjib tous les produits li kaynin f had subCategory
        $products = $subCategoryRepository->find($id)->getProducts();
        // hna kanjib l'objet subCategory b id
        $subCategory = $subCategoryRepository->find($id);
        // afficher filter.html.twig m3a produits, subCategory et categories
        return $this->render('home/filter.html.twig', [
            'products'   => $products,
            'subCategory'=> $subCategory,
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
