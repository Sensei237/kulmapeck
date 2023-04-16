<?php

namespace App\Controller\Api\Controller\Etablissement;

use App\Repository\EtablissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AllController extends AbstractController
{

    public function __construct(
        private EtablissementRepository $etablissementRepository
    ) {
    }

    public function __invoke(): array
    {
        return $this->etablissementRepository->findBy([], ['name' => 'ASC']);
    }
}
