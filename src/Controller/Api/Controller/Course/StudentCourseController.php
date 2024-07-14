<?php

namespace App\Controller\Api\Controller\Course;

use App\Entity\Eleve;
use App\Repository\EleveRepository;
use App\Repository\LectureRepository;
use ArrayObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class StudentCourseController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private LectureRepository $lectureRepository
    ) {
    }

    public function __invoke(Eleve $eleve): ArrayObject
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);
    
        if ($eleve !== $eleveConnected || $eleveConnected == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

       
        $courses = $eleveConnected->getCours();
        $tab = [];
        foreach ($courses as $course) {
            $numberOfLessons = 0;
            $finishedLessons = 0;
            foreach ($course->getChapitres() as $chapitre) {
                foreach ($chapitre->getLessons() as $lesson) {
                    $lecture = $this->lectureRepository->findOneBy(['lesson' => $lesson, 'eleve' => $eleve]);
                    if ($lecture !== null) {
                        $finishedLessons++;
                    }
                    $numberOfLessons++;
                }
            }

            if ($numberOfLessons == 0) {
                $numberOfLessons = 1;
            }
            $tab[] = [
                'course' => $course,
                'numberOfLessons' => $numberOfLessons,
                'finishedLessons' => $finishedLessons,
                'percent' => $finishedLessons*100/$numberOfLessons,
            ];
        }

        return new ArrayObject($tab);
    }
}
