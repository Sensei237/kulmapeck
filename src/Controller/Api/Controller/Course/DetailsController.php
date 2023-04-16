<?php

namespace App\Controller\Api\Controller\Course;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DetailsController extends AbstractController
{

    public function __construct(
        private CoursRepository $coursRepository
    )
    {
        
    }

    public function __invoke(Cours $cours): Cours
    {
        $cours->setVues($cours->getVues()+1);
        $this->coursRepository->save($cours, true);
        return $cours;
    }
}