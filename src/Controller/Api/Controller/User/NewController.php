<?php

namespace App\Controller\Api\Controller\User;

use App\Entity\User;
use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NewController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private PersonneRepository $personneRepository,
        private EmailVerifier $emailVerifier
    ) {
    }

    public function __invoke(Request $request): ?User
    {
        $user = new User();

        return $user;
    }

}
