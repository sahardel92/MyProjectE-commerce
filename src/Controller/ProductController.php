<?php


namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\AddProductHistoryType;
use App\Form\ProductUpdateType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

//  Had fichier huwa controller li kayhandle CRUD dyal Product (affichage, ajout, modification, suppression, stock)

#[Route('/editor/product')]
final class ProductController extends AbstractController
{
    //  Affichage dial liste dial produits
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

      // Création produit jdid
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


         // Vérification formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            // Si kayna image kanuploadiwha b smiya unique
            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_dir'),
                        $newFileName
                    );
                } catch (FileException $exception) {
                    //  Exception f upload (f had cas ma ndirou walou, n9dro nzido flash msg)
                }

               
                $product->setImage($newFileName);
            }

            // Sauvegarde produit
            $entityManager->persist($product);
            $entityManager->flush();

            // Initialisation historique dyal stock
            $stockHistory = new AddProductHistory();
            $stockHistory->setQte($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($stockHistory);
            $entityManager->flush();

                //  Message succès
            $this->addFlash('success', 'Produit ajouté avec succès !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

     //  Affichage produit wahed
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

     //  Modification produit
    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

             // Vérification si image kayna
            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_dir'),
                        $newFileName
                    );
                } catch (FileException $exception) {
                     //  Exception f upload
                    
                }

                
                $product->setImage($newFileName);
            }

            // Sauvegarde modifications
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

     // Suppression produit
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('danger', 'Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

      // Ajout stock jdida l produit
    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['POST', 'GET'])]
    public function addStock($id, EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository): Response
    {
    $addStock = new AddProductHistory();
    $form = $this->createForm(AddProductHistoryType::class, $addStock);
    $form->handleRequest($request);

    $product = $productRepository->find($id);

     if($form->isSubmitted() && $form->isValid()) {

          // Vérification qte > 0
        if ($addStock->getQte() >0) {

            $newQte = $product->getStock() + $addStock->getQte();
            $product->setStock($newQte);

            // Sauvegarde historique
            $addStock->setCreatedAt(new \DateTimeImmutable());
            $addStock->setProduct($product);
            $entityManager->persist($addStock);
            $entityManager->flush();

            $this->addFlash('success', 'Le stock a été modifié avec succès !');


            return $this->redirectToRoute('app_product_index');

        }else{

            // Si qte khassha tkon > 0
            $this->addFlash('danger', 'Le stock doit être positif !');
            return $this->redirectToRoute('app_product_stock_add', ['id' => $product->getId()]);

        }
       }  
         return $this->render('product/addStock.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }


    // Affichage historique dial stock ajouté
    #[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]

    public function productAddHistory($id, ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository): Response
    {
        $product = $productRepository->find($id);
        $productAddHistory = $addProductHistoryRepository->findBy(['product' => $product],['id'=>'DESC']);

        return $this->render('product/addedStockHistoryShow.html.twig', [
            'productAddHistory' => $productAddHistory,
            'product' => $product,
        ]);

    }

}