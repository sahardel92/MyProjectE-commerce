<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;


class GoogleAuthenticator extends OAuth2Authenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private RouterInterface $router
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        /** @var GoogleClient $client */
        $client = $this->clientRegistry->getClient('google');

        try {
            /** @var GoogleUser $googleUser */
            $googleUser = $client->fetchUser();
        } catch (\Throwable $e) {
            throw new CustomUserMessageAuthenticationException('Connexion Google échouée.');
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $fullName = $googleUser->getName() ?? trim(($googleUser->getFirstName() ?? '').' '.($googleUser->getLastName() ?? ''));

        return new SelfValidatingPassport(
            new UserBadge($email, function () use ($email, $googleId, $fullName) {
                $user = $this->userRepository->findOneBy(['email' => $email]);

                if (!$user) {
                    $user = new User();
                    $user->setEmail($email);
                    if (method_exists($user, 'setFullName') && $fullName) {
                        $user->setFullName($fullName);
                    }
                    if (method_exists($user, 'setGoogleId')) {
                        $user->setGoogleId($googleId);
                    }
                    // si password requis en DB
                    if (method_exists($user, 'setPassword') && !$user->getPassword()) {
                        $user->setPassword(bin2hex(random_bytes(16)));
                    }
                    $this->em->persist($user);
                    $this->em->flush();
                } else {
                    if (method_exists($user, 'setGoogleId') && method_exists($user, 'getGoogleId') && !$user->getGoogleId()) {
                        $user->setGoogleId($googleId);
                        $this->em->flush();
                    }
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, \Throwable $exception): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
