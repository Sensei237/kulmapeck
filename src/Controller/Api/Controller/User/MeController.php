<?php

namespace App\Controller\Api\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MeController extends AbstractController
{

    public function __construct(
        private Security $security
    ) {
    }

    public function __invoke(): ?User
    {
        $user = $this->security->getUser();

        return $user;
    }
}
