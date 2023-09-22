<?php

namespace App\Controller\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends AbstractController
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login()
    {
        $user = $this->getUser();

        if (null === $user ) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($this->authorizationChecker->isGranted('ROLE_STUDENT')) {
            return $this->json([
                'message' => 'Accès autorisé uniquement aux apprenants',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ( !in_array('ROLE_STUDENT', $user->getRoles())) {
            return $this->json([
                'message' => 'Accès autorisé uniquement aux apprenants',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
