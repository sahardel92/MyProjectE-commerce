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
        // hna kancréé review jdida
        $review = new Review();
        // n3tiha produit li kayn (men route /product/{id})
        $review->setProduct($product);
        // n3tiha user li connecté daba
        $review->setUser($this->getUser()); // utilisateur connecté
        // n3tiha date dial création
        $review->setCreatedAt(new \DateTimeImmutable());

            // kancréé formulaire m3a ReviewType
            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

            // ila formulaire tsift o valid
        if ($form->isSubmitted() && $form->isValid()) {
             // kan save review f DB
                $em->persist($review);
                $em->flush();

                $this->addFlash('success', 'Votre avis a été ajouté !');
                // redirection vers page produit bach nchofo avis jdide
                return $this->redirectToRoute('app_home_product_show', ['id' => $product->getId()]);
            }
            // sinon afficher formulaire review
        return $this->render('review/new.html.twig', [
                'form' => $form->createView(),
                'product' => $product,
            ]);
        }


    #[Route('/home/product/{id}/show', name: 'app_product_show')]
    public function show(Product $product, EntityManagerInterface $em): Response
    {
        // hna kanjib tous les avis dial had produit, triés b date DESC
        $reviews = $em->getRepository(Review::class)->findBy(
            ['product' => $product],
            ['createdAt' => 'DESC']
        );
         dd($reviews); // debug bach nchofo contenu reviews

          // afficher page produit m3a liste reviews
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
}
