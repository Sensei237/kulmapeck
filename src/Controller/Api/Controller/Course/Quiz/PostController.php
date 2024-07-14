<?php

namespace App\Controller\Api\Controller\Course\Quiz;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Lecture;
use App\Entity\QuizLost;
use App\Entity\QuizResult;
use App\Repository\ChapitreRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\LectureRepository;
use App\Repository\PaymentRepository;
use App\Repository\QuizLostRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class PostController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private PaymentRepository $paymentRepository,
        private ChapitreRepository $chapitreRepository,
        private QuizLostRepository $quizLostRepository,
        private EntityManagerInterface $entityManager,
        private LectureRepository $lectureRepository,
        private QuizResultRepository $quizResultRepository,
        private QuizRepository $quizRepository,
        private CoursRepository $coursRepository
    ) {
    }

    public function __invoke(Chapitre $chapitre, Request $request): \ArrayObject
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            throw new BadRequestHttpException("Vous devez être connecté");
        }

        // $data = $request->attributes->getIterator()['data'];
        // if ($data instanceof QuizResult) {
        //     $data->setEleve($eleveConnected);

        //     return $data;
        // }

        return $this->correction($this->paymentRepository, $this->chapitreRepository, $this->quizLostRepository, $request, $this->entityManager, $this->lectureRepository, $this->quizResultRepository, $this->eleveRepository, $this->quizRepository);
    }

    public function correction(
        PaymentRepository $paymentRepository,
        ChapitreRepository $chapitreRepository,
        QuizLostRepository $quizLostRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        LectureRepository $lectureRepository,
        QuizResultRepository $quizResultRepository,
        EleveRepository $eleveRepository,
        QuizRepository $quizRepository
    ): \ArrayObject {
        $user = $this->security->getUser();

        $eleve = $eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve === null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        $data = $request->toArray();
        $cours = $this->coursRepository->find($data['cours_id']);
        $chapitre = $this->chapitreRepository->findOneBy(['slug' => $data['chapitre_slug']]);

        // On verifie si l'élève n'a pas déjà ce cours dans sa liste des cours
        // le cours soit souscrire a un compte premium
        if (!$eleve->getCours()->contains($cours)) {
            throw $this->createAccessDeniedException("Le cours ne figure pas dans votre liste d'apprentissage");
        }

        if ($chapitre !== null) {
            $lecture = $lectureRepository->findOneBy(['eleve' => $eleve, 'chapitre' => $chapitre]);
            if ($lecture == null) {
                $lecture = new Lecture();
                $lecture->setChapitre($chapitre)->setEleve($eleve)->setIsFinished(false)->setReference(time() + $eleve->getId())->setStartAt(new \DateTimeImmutable());
                $lectureRepository->save($lecture, true);
            }
            $quizLost = $quizLostRepository->findOneBy(['chapitre' => $chapitre, 'eleve' => $eleve]);
        } else {
            $lecture = $lectureRepository->findOneBy(['eleve' => $eleve, 'cours' => $cours]);
            $lecture->setCours($cours)->setEleve($eleve)->setIsFinished(false)->setReference(time()
                + $eleve->getId())->setStartAt(new \DateTimeImmutable());
            $lectureRepository->save($lecture, true);
            $quizLost = $quizLostRepository->findOneBy(['cours' => $cours, 'eleve' => $eleve]);
        }

        $noteQuiz = 0;
        $quizzes = $data['quizzes'];
        foreach ($quizzes as $quizze) {
            $quizId = $quizze['id'];
            $results = $quizze['reponses'];
            $quiz = $quizRepository->find($quizId);

            if ($quiz === null) {
                throw new BadRequestException("Données corrompues");
            }

            $isCorrect = false;
            $note = 0;

            if ($results == $quiz->getPropositionJuste()) {
                $isCorrect = true;
                $note = 20 / count($chapitre->getQuizzes());
                $noteQuiz += $note;
            }

            $quizResult = $quizResultRepository->findOneBy(['quiz' => $quiz, 'eleve' => $eleve]);


            if ($quizResult === null) {

                $quizResult = new QuizResult();
                $quizResult->setEleve($eleve)->setQuiz($quiz);
            }

            $quizResult->setIsCorrect($isCorrect)
                ->setResult($results)
                ->setNote($note)
                ->setUpdatedAt(new \DateTimeImmutable());

            $quizResultRepository->save($quizResult, true);
        }


        if ($eleve !== null) {
            if ($lecture === null) {
                $lecture = new Lecture();
                if ($chapitre !== null) {
                    $lecture->setChapitre($chapitre);
                } else {
                    $lecture->setCours($cours);
                }
                $lecture->setEleve($eleve)->setReference(time() + $quiz->getId())->setEndAt(new \DateTimeImmutable());
            }
            $isFinished = false;
            if (($noteQuiz * 100) / 20 >= 50) {
                $isFinished = true;
            }
            $lecture->setIsFinished($isFinished)->setNote($noteQuiz);

            if ($isFinished) {
                if ($lecture->getChapitre()) {
                    $nextChapNumber = $lecture->getChapitre()->getNumero() + 1;
                    $nextChapter = $chapitreRepository->findOneBy(['numero' => $nextChapNumber, 'cours' => $lecture->getChapitre()->getCours()]);
                    if ($nextChapter && count($nextChapter->getLessons()) > 0) {
                        $newLecture = new Lecture();
                        $newLecture->setLesson($nextChapter->getLessons()[0])
                            ->setEleve($eleve)->setStartAt(new \DateTimeImmutable())->setReference(time() + $nextChapter->getId())
                            ->setIsFinished(false);
                        $lectureRepository->save($newLecture);
                    }
                } else {
                    // il n'y a plus de chapitres donc on passe au quiz de fin de chapitre
                    $newLecture = new Lecture();
                    $newLecture->setCours($cours)
                        ->setEleve($eleve)->setStartAt(new \DateTimeImmutable())->setReference(time() + $cours->getId())
                        ->setIsFinished(false);
                    $lectureRepository->save($newLecture);
                }
            }

            $lectureRepository->save($lecture);

            if (!$isFinished) {
                if ($quizLost === null) {
                    $quizLost = new QuizLost();
                    if ($chapitre) {
                        $quizLost->setChapitre($chapitre);
                    } else {
                        $quizLost->setCours($cours);
                    }
                    $quizLost->setEleve($eleve);
                }
                $quizLost->setAttempt($quizLost->getAttempt() + 1)
                    ->setLastAt(new \DateTimeImmutable())
                    ->setNextAt(new \DateTimeImmutable(date('Y-m-d H:i:s', strtotime('+10 second'))))
                    ->setIsOk(false);
                $quizLostRepository->save($quizLost);
            } else {
                if ($quizLost !== null) {
                    $quizLost->setLastAt(new \DateTimeImmutable())
                        ->setIsOk(true);
                    $quizLostRepository->save($quizLost);
                }
            }
        }

        return new \ArrayObject([
            'hasDone' => true,
            'note' => $noteQuiz,
            'quizzesResults' => [],
        ]);
    }
}
