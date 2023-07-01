<?php 
namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\Cours;
use App\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseForum extends AbstractController
{
    public function __invoke(Cours $cours): Forum
    {
        return $cours->getForum();
    }
}
