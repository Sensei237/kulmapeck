<?php

namespace App\Controller\Api\Controller\Course\Lesson;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Forum;
use App\Entity\Lecture;
use App\Entity\Membre;
use App\Repository\EleveRepository;
use App\Repository\ForumRepository;
use App\Repository\LectureRepository;
use App\Repository\LessonRepository;
use App\Repository\MembreRepository;
use App\Repository\PaymentRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class FinishedController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private PaymentRepository $paymentRepository,
        private MembreRepository $membreRepository,
        private ForumRepository $forumRepository,
        private LectureRepository $lectureRepository,
        private EntityManagerInterface $entityManagerInterface,
        private LessonRepository $lessonRepository
    ) {
    }

    public function __invoke(Lecture $lecture): ArrayObject
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            return new ArrayObject(['error' => true, 'message' => 'Vous devez etre élève']);
        }

        return $this->lectureIsFinished(eleve: $eleveConnected, lecture: $lecture);
    }

    public function lectureIsFinished(Lecture $lecture, Eleve $eleve)
    {

        $lecture->setIsFinished(true)->setEndAt(new \DateTimeImmutable());
        $this->lectureRepository->save($lecture, true);

        $lesson = $this->lessonRepository->findOneBy(['chapitre' => $lecture->getLesson()->getChapitre(), 'numero' => $lecture->getLesson()->getNumero() + 1]);
        if ($lesson === null) {
            return new ArrayObject([
                'notAuthorized' => false,
                'showQuizzes' => true,
                'chapterId' => $lecture->getLesson()->getChapitre()->getId(),
            ]);
        }

        $lecture = null;
        if ($eleve !== null) {
            $lecture = $this->lectureRepository->findOneBy(['lesson' => $lesson, 'eleve' => $eleve]);
            if ($lecture === null) {
                $lecture = new Lecture();
                $lecture->setReference($lesson->getId() + time())->setEleve($eleve)->setLesson($lesson)->setIsFinished(false)->setStartAt(new \DateTimeImmutable());
                $this->lectureRepository->save($lecture, true);
            }
        }

        return new ArrayObject([
            'notAuthorized' => false,
            'lesson' => $lesson,
            'lecture' => $lecture ? $lecture->getId() : null,
            'showQuizzes' => false,
        ]);
    }
}