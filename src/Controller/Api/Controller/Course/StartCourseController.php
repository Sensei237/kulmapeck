<?php

namespace App\Controller\Api\Controller\Course;

use App\Entity\Cours;
use App\Entity\Forum;
use App\Entity\Lecture;
use App\Entity\Membre;
use App\Repository\EleveRepository;
use App\Repository\ForumRepository;
use App\Repository\LectureRepository;
use App\Repository\MembreRepository;
use App\Repository\PaymentRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[ AsController ]

class StartCourseController extends AbstractController
 {
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private PaymentRepository $paymentRepository,
        private MembreRepository $membreRepository,
        private ForumRepository $forumRepository,
        private LectureRepository $lectureRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {
    }

    public function __invoke( Cours $course, Request $request ): ArrayObject
 {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy( [ 'utilisateur' => $user ] );

        if ( $eleveConnected === null ) {
            throw $this->createAccessDeniedException( 'Vous devez être connecté !' );
        }

        return $this->startCourse(
            course: $course,
            request: $request,
            paymentRepository: $this->paymentRepository,
            membreRepository: $this->membreRepository,
            forumRepository: $this->forumRepository,
            eleveRepository: $this->eleveRepository,
            lectureRepository: $this->lectureRepository,
            entityManagerInterface: $this->entityManagerInterface
        );
    }

    private function startCourse( Cours $course, Request $request, PaymentRepository $paymentRepository, MembreRepository $membreRepository, ForumRepository $forumRepository, EleveRepository $eleveRepository, LectureRepository $lectureRepository, EntityManagerInterface $entityManagerInterface )
 {
        $eleve = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $eleve === null ) {
            throw new BadRequestHttpException( "Vous devez être connecté en tant qu'élève" );
        }

        // On verifie si l'élève n'a pas déjà ce cours dans sa liste des cours
        // le cours soit souscrire a un compte premium
        if ( !$course->isIsFree() && ( !$eleve->isIsPremium() && ( !$paymentRepository->findOneBy( [ 'eleve' => $eleve, 'cours' => $course, 'isExpired' => false ] ) ) ) ) {

            return new ArrayObject(
                [ 'notAuthorized' => true, 'message' => ' Vous devez payer ce cours pour continuer, ou souscrire à un abonnement pour accéder à tous les cours librement.' ]
            );
        }

        if ( !$eleve->getCours()->contains( $course ) ) {
            if ( $course->isIsFree() || $eleve->isIsPremium() ) {
                $membre = $membreRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
                if ( $membre === null ) {
                    $membre = new Membre();
                    $membre->setUtilisateur( $this->getUser() );
                }
                if ( !$course->getForum() ) {
                    $forum = new Forum();
                    $forum->setCours( $course );
                    $forumRepository->save( $forum, true );
                    $course->setForum( $forum );
                }
                $course->getForum()->addMembre( $membre );
                // On ajoute le cours dans sa liste
                $eleve->addCour( $course );
                $membreRepository->save( $membre, true );
            } else {
                // On lui demande de soit ajouter payer le cours soit devenir premium
                return new ArrayObject(
                    [ 'notAuthorized' => true, 'message' => ' Vous devez payer ce cours pour continuer, ou souscrire à un abonnement pour accéder à tous les cours librement.' ]
                );
            }
        }
        // else {
        //     if ( !$eleve->isIsPremium() || !$paymentRepository->findOneBy( [ 'eleve' => $eleve, 'cours' => $course, 'isExpired' => false ] ) ) {
        //         return new ArrayObject(
        //             [ 'notAuthorized' => true, 'message' => 'Vous devez payer le cours pour continuer' ]
        // );
        //     }
        // }

        if ( $course->isIsFree() || $eleve->isIsPremium() || $eleve->getCours()->contains( $course ) ) {

            $chapitre = $course->getChapitres()[ 0 ];
            $lesson = null;
            $lecture = null;
            if ( $eleve !== null ) {
                $lecture = $lectureRepository->findStudentLastLecture( $eleve, $course );
                if ( $lecture ) {
                    $lesson = $lecture->getLesson();
                }
            }
            if ( $lesson ===  null ) {
                $lesson = $chapitre->getLessons()[ 0 ];
                if ( $eleve !== null ) {
                    $lecture = new Lecture();
                    $lecture->setReference( $lesson->getId() + time() )->setEleve( $eleve )->setLesson( $lesson )->setIsFinished( false )->setStartAt( new \DateTimeImmutable() );
                    $lectureRepository->save( $lecture, true );
                }
            }

            $entityManagerInterface->flush();

            return new ArrayObject(
                [
                    'notAuthorized' => false,
                    'lesson' => $lesson,
                    'lecture' => $lecture ? $lecture->getId() : null,
                    'message' => '',
                    'chapitreNum'=>$chapitre->getNumero(),
                    //'chapitrename'=>$chapitre->getIntitule(),
                    'lessonNum'=>$lesson->getNumero(),

                ]
            );
        } else {
            return new ArrayObject(
                [ 'notAuthorized' => true, 'message' => ' Vous devez payer ce cours pour continuer, ou souscrire à un abonnement pour accéder à tous les cours librement.' ]
            );
        }
    }

}
