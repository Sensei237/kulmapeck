<?php

namespace App\Controller\Api\Controller\Course\Review;

use App\Entity\Cours;
use Doctrine\Common\Collections\Collection;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class CourseReviewController extends AbstractController
{

    public function __invoke(Cours $cours): Collection
    {
        return $cours->getReviews();
    }
}