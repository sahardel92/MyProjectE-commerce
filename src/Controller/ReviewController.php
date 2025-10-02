<?php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReviewController extends AbstractController
{
    #[Route('/product/{id}/review', name: 'app_review_new')]
    public function new(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $review = new Review();
        $review->setProduct($product);
        $review->setUser($this->getUser()); // utilisateur connecté
        $review->setCreatedAt(new \DateTimeImmutable());

            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($review);
                $em->flush();

                $this->addFlash('success', 'Votre avis a été ajouté !');
                return $this->redirectToRoute('app_home_product_show', ['id' => $product->getId()]);
            }

        return $this->render('review/new.html.twig', [
                'form' => $form->createView(),
                'product' => $product,
            ]);
        }


    #[Route('/home/product/{id}/show', name: 'app_product_show')]
    public function show(Product $product, EntityManagerInterface $em): Response
    {
        $reviews = $em->getRepository(Review::class)->findBy(
            ['product' => $product],
            ['createdAt' => 'DESC']
        );
         dd($reviews); 

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
}
