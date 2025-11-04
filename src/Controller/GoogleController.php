<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connect(ClientRegistry $clientRegistry)
    {
        // redirige l’utilisateur vers Google
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile']); // permissions demandées
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(): Response
    {
        // Cette route est appelée automatiquement après le retour de Google
        // L’authenticator prend le relais (pas besoin de code ici)
        return $this->redirectToRoute('app_home');
    }
}
