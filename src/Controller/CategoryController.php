<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;   // <= la SEULE Request
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response

    {

        // hna kanjib toutes les catégories men la base de données
        $categories = $categoryRepository->findAll();

        // hna kan afficher page category/index.html.twig m3a liste categories

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        // hna kancréé catégorie jdida (vide f loul)
        $category = new Category();

        // hna kancréé form m3a CategoryFormType bach nrempli catégorie
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

         // ila formulaire t3emr o valid → save f DB
        if($form->isSubmitted() && $form->isValid()){
           $entityManager->persist($category);
           $entityManager->flush();

              $this->addFlash('success', 'Catégorie ajoutée avec succès !');
       
             // redirection vers liste categories
        return $this->redirectToRoute('app_category');
        }
        // sinon afficher formulaire
        return $this->render('category/new.html.twig',['form'=>$form->createView()]);

    }


    #[Route('/admin/category/{id}/update', name: 'app_category_update')]
    public function update(Category $category, EntityManagerInterface $entityManager, Request $request):Response
    {
        // formulaire m3a catégorie existante (pré-rempli)
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        // ila formulaire t3emr o valid → update DB
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
              $this->addFlash('success', 'Catégorie modifiée avec succès !');
            return $this->redirectToRoute('app_category');
          
        }
        // sinon afficher formulaire update
        return $this->render('category/update.html.twig',['form'=>$form->createView()]);



    }

    #[Route('/admin/category/{id}/delete', name: 'app_category_delete')]
    public function delete(Category $category, EntityManagerInterface $entityManager):Response
    {
        // supprimer catégorie men DB
        $entityManager->remove($category);
        $entityManager->flush();
        // message suppression
        $this->addFlash('danger', 'Catégorie supprimée avec succès !');

         // redirection vers liste categories
        return $this->redirectToRoute('app_category');
    

    
    }

}