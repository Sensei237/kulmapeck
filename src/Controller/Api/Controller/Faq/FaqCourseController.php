<?php 

namespace App\Controller\Api\Controller\Faq;

use App\Entity\Cours;
use App\Repository\FAQRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class FaqCourseController extends AbstractController
{

    public function __construct(
        private FAQRepository $fAQRepository
    ) {
    }

    public function __invoke(Cours $cours): array
    {
        return $this->fAQRepository->findBy(['cours' => $cours], ['id' => 'ASC']);
    }
}
