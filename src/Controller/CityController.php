<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/editor/city')]
final class CityController extends AbstractController
{
    #[Route(name: 'app_city_index', methods: ['GET'])]
    public function index(CityRepository $cityRepository): Response
    {
        // hna kanjib liste dial les villes men DB o kan3tiha l twig

        return $this->render('city/index.html.twig', [
            // hna kancréé objet city (vide f loul)
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
         // kancréé form m3a CityType
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        // ila formulaire tsift o valid, save city f DB
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($city); // préparer insertion
            $entityManager->flush();// executer save
            // redirection vers la liste des villes
            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }
        // sinon afficher formulaire new
        return $this->render('city/new.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    public function show(City $city): Response
    {
        // hna kan afficher une seule ville (show page)
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
         // hna formulaire dyal edit (déjà rempli b city li kayna)
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

         // ila formulaire valid → save modification
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
             // redirection vers la liste
            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }
        // sinon afficher formulaire edit
        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        // hna kandir check CSRF bach nverifi had la requete hiya sahi7a
         // ila valid → supprimer
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();
        }
        // redirection vers liste dial villes
        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
