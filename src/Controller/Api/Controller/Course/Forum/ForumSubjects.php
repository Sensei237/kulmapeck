<?php
namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumSubjects extends AbstractController
{
    public function __invoke(Forum $forum)
    {
        return $forum->getSujets();
    }
}
