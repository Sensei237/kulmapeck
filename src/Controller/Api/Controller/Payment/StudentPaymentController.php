<?php

namespace App\Controller\Api\Controller\Payment;

use App\Entity\Eleve;
use Doctrine\Common\Collections\Collection;
use App\Repository\EleveRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class StudentPaymentController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security
    ) {
    }

    public function __invoke(Eleve $eleve): Collection
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve !== $eleveConnected || $eleveConnected == null) {
            throw new BadRequestHttpException("Vous devez être connecté");
        }

        return $eleveConnected->getPayments();
    }
}
