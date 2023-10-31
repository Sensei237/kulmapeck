<?php

namespace App\Controller\Instructor;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\FAQ;
use App\Entity\Lesson;
use App\Entity\Notification;
use App\Entity\Quiz;
use App\Form\Cours1Type;
use App\Form\LessonFormType;
use App\Form\QuizType;
use App\Repository\ChapitreRepository;
use App\Repository\CoursRepository;
use App\Repository\EnseignantRepository;
use App\Repository\FAQRepository;
use App\Repository\ForumRepository;
use App\Repository\LessonRepository;
use App\Repository\NotificationTypeRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Utils\NotificationCodes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ Route( '/instructor/courses' ) ]

class CoursesController extends AbstractController
 {
    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager )
 {
        $this->entityManager = $entityManager;
    }

    #[ Route( '/', name: 'app_instructor_courses' ) ]

    public function index( Request $request, EnseignantRepository $enseignantRepository, CoursRepository $coursRepository, PaginatorInterface $paginatorInterface ): Response
 {
        $enseignant = $enseignantRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $enseignant === null ) {
            throw $this->createAccessDeniedException();
        }

        if ( $request->get( 'search' ) !== null ) {
            $courses = $coursRepository->searchByInstructor( $enseignant, $request->get( 'search' ) );
        } else {
            switch ( $request->get( 'filter' ) ) {
                case 'free':
                $courses = $coursRepository->findBy( [ 'enseignant' => $enseignant, 'isFree' => true ], [ 'isPublished' => 'ASC', 'createdAt' => 'DESC' ] );
                break;
                case 'newest':
                $courses = $coursRepository->findBy( [ 'enseignant' => $enseignant, ], [ 'createdAt' => 'DESC' ] );
                break;
                case 'premium':
                $courses = $coursRepository->findBy( [ 'enseignant' => $enseignant, 'isFree' => false ], [ 'isPublished' => 'ASC', 'createdAt' => 'DESC' ] );
                break;
                case 'viewed':
                $courses = $coursRepository->findBy( [ 'enseignant' => $enseignant ], [ 'vues' => 'DESC' ] );
                break;
                default:
                $courses = $coursRepository->findBy( [ 'enseignant' => $enseignant ], [ 'isPublished' => 'ASC', 'createdAt' => 'DESC' ] );
                break;
            }
        }

        return $this->render( 'instructor/courses/index.html.twig', [
            'courses' => $paginatorInterface->paginate( $courses, $request->query->getInt( 'page', 1 ), 10 ),
            'instructorCourses' => true,
            'filter' => $request->get( 'filter' ),
            'search' => $request->get( 'search' ),
        ] );
    }

    #[ Route( '/{slug}/preview', name: 'app_instructor_courses_preview' ) ]

    public function coursePreview( Cours $cours, Request $request ): Response
 {

        return $this->render( 'instructor/courses/course_details.html.twig', [
            'course' => $cours,
            'isTeacher' => true,
            'instructorCourses' => true,
        ] );
    }

    #[ Route( '/{slug}/edit', name: 'app_instructor_courses_edit' ) ]
    #[ Route( '/new', name: 'app_instructor_courses_new' ) ]

    public function edit( LessonRepository $lessonRepository, Cours $cours = null,
    NotificationTypeRepository $notificationTypeRepository,
    UserRepository $userRepository, Request $request, SluggerInterface $slugger,

    ForumRepository $forumRepository, CoursRepository $coursRepository,
    EnseignantRepository $enseignantRepository, FileUploader $fileUploader ): Response
 {
        $enseignant = $enseignantRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $enseignant === null || !$enseignant->isIsCertified() || ( $cours !== null && $enseignant->getId() !== $cours->getEnseignant()->getId() ) ) {
            throw $this->createAccessDeniedException();
        }

        $isEditionMode = true;
        if ( $cours === null ) {
            $cours = new Cours();
            $isEditionMode = false;
            $pageTitle = 'Submit a new Course';
        } else {
            $pageTitle = 'Edit Course <br>' . $cours->getIntitule();
        }
        $form = $this->createForm( Cours1Type::class, $cours );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            if ( $cours->getSlug() === null ) {
                $cours->setSlug( $slugger->slug( time() . '-' . $cours->getIntitule() ) );
            }
            $cours->setEnseignant( $enseignant );

            if ( $isEditionMode && $fileUploader ) {
                $this->uploadCourseMedia( $cours, $fileUploader );
            } else if ( !$isEditionMode && $fileUploader ) {
                $this->uploadCourseMedia( $cours, $fileUploader );

            }

            foreach ( $cours->getChapitres() as $chapitre ) {
                if ( $chapitre->getSlug() === null ) {
                    $chapitre->setSlug( $slugger->slug( $chapitre->getTitle() . ' - ' . time() ) );
                }
                $chapitre->setCours( $cours );
                foreach ( $chapitre->getLessons() as $lesson ) {
                    if ( $lesson->getSlug() === null ) {
                        $lesson->setSlug( $slugger->slug( $lesson->getTitle() . ' ' . time() ) );
                    }
                    $lesson->setUpdatedAt( new \DateTimeImmutable() );
                    $lesson->setChapitre( $chapitre );
                    $lessonRepository->save( $lesson );
                }
            }
            $cours->setUpdatedAt( new \DateTimeImmutable() );
            $coursRepository->save( $cours, true );
            $admins = $userRepository->findBy( [ 'isAdmin' => true, 'isBlocked' => false, 'isVerified' => true ] );
            $notificationType = $notificationTypeRepository->findOneBy( [ 'type' => NotificationCodes::COURSE_IS_CREATED ] );
            if ( $notificationType && $notificationType->getNotificationTemplate() ) {
                $content = $notificationType->getNotificationTemplate()->getTemplate();
                if ( $content !== null ) {
                    foreach ( $admins as $admin ) {
                        if ( in_array( [ 'ROLE_COURSES_MANAGER', 'ROLE_SUPER_USER' ], $admin->getRoles() ) ) {
                            if ( $content ) {
                                $notification = new Notification();
                                $notification->setDestinataire( $admin )
                                ->setType( NotificationCodes::COURSE_IS_CREATED )
                                ->setContent( $content )
                                ->setTitle( $notificationType->getLabel() )
                                ;
                            }
                        }
                    }
                }
            }

            return $this->redirectToRoute( 'app_instructor_courses', [], Response::HTTP_SEE_OTHER );
        }

        $lessonForm = $this->createForm( LessonFormType::class );

        return $this->render( 'instructor/courses/edit.html.twig', [
            'form' => $form->createView(),
            'lessonForm' => $lessonForm->createView(),
            'cours' => $cours,
            'isEditionMode' => $isEditionMode,
            'pageTitle' => $pageTitle,
            'instructorCourses' => true,
        ] );
    }

    #[ Route( '/{slug}/quizzes', name: 'app_instructor_courses_quizzes', methods: [ 'POST', 'GET' ] ) ]

    public function newQuiz( Chapitre $chapitre, Request $request,
    QuizRepository $quizRepository )
 {
        if ( $chapitre->getCours()->isIsValidated() ) {
            throw $this->createAccessDeniedException( 'Vous ne pouvez plus modofier ce cours' );
        }

        $quiz = new Quiz();
        $form = $this->createForm( QuizType::class, $quiz );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $quiz->setChapitre( $chapitre )
            ->setCours( $chapitre->getCours() )
            ->setReference( time()+$quiz->getId() );

            $quizRepository->save( $quiz, true );
            $this->addFlash( 'info', 'Quiz Chapitre - '.$chapitre->getTitle() );
            return $this->redirectToRoute( 'app_instructor_courses_quizzes', [ 'slug' => $chapitre->getSlug() ] );
        }

        return $this->render( 'instructor/courses/quiz/index.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form->createView(),
            'instructorCourses' => true,
            'imageFile' => $chapitre->getCours()->getMedia()->getImageFile(),
            'title' => 'Quiz - '.$chapitre->getTitle(),
            'quizzes' => $this->entityManager->getRepository( Quiz::class )->findBy( [ 'chapitre' => $chapitre ] ),
            // 'quizzes' => $chapitre->getCours()->getQuizzes(),
            'deleteRedirectUri' => $this->generateUrl( 'app_instructor_courses_quizzes_final', [ 'slug' => $chapitre->getSlug() ] )
        ] );
    }

    #[ Route( '/{slug}/final-quizzes', name: 'app_instructor_courses_quizzes_final', methods: [ 'POST', 'GET' ] ) ]

    public function editQuizFinal( Cours $cours, Request $request, QuizRepository $quizRepository )
 {
        if ( $cours->isIsValidated() ) {
            throw $this->createAccessDeniedException( 'Vous ne pouvez plus modifier ce cours' );
        }

        $quiz = new Quiz();
        $form = $this->createForm( QuizType::class, $quiz );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $quiz->setCours( $cours )
            ->setReference( time() + $quiz->getId() )
            ->setChapitre( null );

            $quizRepository->save( $quiz, true );
            $this->addFlash( 'info', 'Quiz Evaluation finale - '.$cours->getIntitule() );

            return $this->redirectToRoute( 'app_instructor_courses_quizzes_final', [ 'slug' => $cours->getSlug() ] );
        }

        return $this->render( 'instructor/courses/quiz/index.html.twig', [
            'course' => $cours,
            'form' => $form->createView(),
            'instructorCourses' => true,
            'imageFile' => $cours->getMedia()->getImageFile(),
            'title' =>'Evaluation finale - '. $cours->getIntitule(),
            //'quizzes' => $cours->getQuizzes(),
            'quizzes' => $this->entityManager->getRepository( Quiz::class )->findBy( [ 'chapitre' => null
            , 'cours'=>$cours ] ),
            'deleteRedirectUri' => $this->generateUrl( 'app_instructor_courses_quizzes_final', [ 'slug' => $cours->getSlug() ] )
        ] );
    }

    #[ Route( '/{slug}/delete', name: 'app_instructor_courses_delete', methods: [ 'POST', 'GET' ] ) ]

    public function deleteCourse( Cours $cours, ChapitreRepository $chapitreRepository, LessonRepository $lessonRepository, EnseignantRepository $enseignantRepository, CoursRepository $coursRepository, Request $request ): Response
 {
        $enseignant = $enseignantRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $enseignant === null || !( $enseignant === $cours->getEnseignant() ) ) {
            throw $this->createAccessDeniedException();
        }

        if ( $cours->isIsPublished() ) {
            throw $this->createAccessDeniedException( 'Opération impossible ! Le cours est déjà publié' );
        }

        foreach ( $cours->getChapitres() as $chapitre ) {
            foreach ( $chapitre->getLessons() as $lesson ) {
                $lessonRepository->remove( $lesson );
            }
            $chapitreRepository->remove( $chapitre );
        }

        $coursRepository->remove( $cours, true );

        if ( $request->isXmlHttpRequest() ) {
            return new JsonResponse( [ 'hasDone' => true ] );
        }

        return $this->redirectToRoute( 'app_instructor_courses' );
    }

    #[ Route( '/{reference}/edit-quiz', name: 'app_instructor_courses_quiz_edit', methods: [ 'POST' ] ) ]

    public function editQuiz( Quiz $quiz, Request $request, QuizRepository $quizRepository )
 {
        $quiz->setQuestion( $request->request->get( 'question' ) )
        ->setProposition1( $request->request->get( 'proposition1' ) )
        ->setProposition2( $request->request->get( 'proposition2' ) )
        ->setProposition3( $request->request->get( 'proposition3' ) )
        ->setProposition4( $request->request->get( 'proposition4' ) )
        ->setPropositionJuste( [ $request->request->get( 'propositionJuste' ) ] );

        $quizRepository->save( $quiz, true );

        return $this->redirectToRoute( 'app_instructor_courses_quizzes', [
            'slug' => $quiz->getChapitre()->getSlug()
        ] );
    }

    #[ Route( '/{reference}/remove-quiz', name: 'app_instructor_courses_quiz_remove' ) ]

    public function deleteQuiz( Quiz $quiz, EnseignantRepository $enseignantRepository, QuizRepository $quizRepository, Request $request ): Response
 {
        $enseignant = $enseignantRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $enseignant === null ) {
            throw $this->createAccessDeniedException();
        }

        if ( ( $quiz->getChapitre() !== null && $quiz->getChapitre()->getCours()->isIsPublished() ) || ( $quiz->getCours() !== null && $quiz->getCours()->isIsValidated() ) ) {
            throw $this->createAccessDeniedException();
        }

        if ( $this->isCsrfTokenValid( 'deletequiz' . $quiz->getId(), $request->query->get( '_token' ) ) ) {
            $quizRepository->remove( $quiz, true );
        }

        return $this->redirect( $request->query->get( 'redirect_uri' ) );
    }

    #[ Route( '/add-chap', name: 'app_instructor_add_chap', methods: [ 'POST' ] ) ]

    public function addChap( Request $request )
 {

        return $this->render( 'instructor/courses/include/_chap_item.html.twig', [
            'title' => $request->request->get( 'title' ),
            'description' => $request->request->get( 'description' ),
            'index' => $request->request->get( 'index' ),
            'numero' => $request->request->getInt( 'numero', 1 ),
        ] );
    }

    #[ Route( '/add-lesson', name: 'app_instructor_add_lesson', methods: [ 'POST' ] ) ]

    public function addLesson( Request $request )
 {

        return $this->render( 'instructor/courses/include/_lesson_item.html.twig', [
            'title' => $request->request->get( 'title' ),
            'description' => $request->request->get( 'description' ),
            'index' => $request->request->get( 'index' ),
            'videoLink' => $request->request->get( 'videoLink' ),
            'chapIndex' => $request->request->get( 'chapIndex' ),
            'numero' => $request->request->getInt( 'numero', 1 ),
        ] );
    }

    #[ Route( '/add-faq', name: 'app_instructor_add_faq', methods: [ 'POST' ] ) ]

    public function addFAQ( Request $request )
 {

        return $this->render( 'instructor/courses/include/_faq_item.html.twig', [
            'question' => $request->request->get( 'question' ),
            'answer' => $request->request->get( 'answer' ),
            'index' => $request->request->get( 'index' ),
        ] );
    }

    #[ Route( '/{id}/delete-faq', name: 'app_instructor_delete_faq', methods: [ 'GET' ] ) ]

    public function deleteFAQ( FAQ $fAQ, FAQRepository $fAQRepository )
 {
        $fAQRepository->remove( $fAQ, true );

        return new JsonResponse( [ 'hasDone' => true ] );
    }

    #[ Route( '/{id}/delete-lesson', name: 'app_instructor_delete_lesson', methods: [ 'GET' ] ) ]

    public function deleteLesson( Lesson $lesson, LessonRepository $lessonRepository )
 {
        $lessonRepository->remove( $lesson, true );

        return new JsonResponse( [ 'hasDone' => true ] );
    }

    #[ Route( '/{id}/delete-chapitre', name: 'app_instructor_delete_chapitre', methods: [ 'GET' ] ) ]

    public function deleteChapitre( Chapitre $chapitre, ChapitreRepository $chapitreRepository )
 {
        $chapitreRepository->remove( $chapitre, true );

        return new JsonResponse( [ 'hasDone' => true ] );
    }

    private function uploadCourseMedia( Cours $cours, FileUploader $fileUploader )
 {
        if ( $cours->getMedia()->imageFileUpload !== null ) {
            $fileName = $fileUploader->upload( $cours->getMedia()->imageFileUpload, 'media/courses' );
            $cours->getMedia()->setImageFile( $fileName );
        }

        if ( $cours->getMedia()->webMFileUpload !== null ) {
            $fileName = $fileUploader->upload( $cours->getMedia()->webMFileUpload, 'media/courses' );
            $cours->getMedia()->setWebMFile( $fileName );
        }

        if ( $cours->getMedia()->oggFileUpload !== null ) {
            $fileName = $fileUploader->upload( $cours->getMedia()->oggFileUpload, 'media/courses' );
            $cours->getMedia()->setOggFile( $fileName );
        }

        if ( $cours->getMedia()->mp4FileUpload !== null ) {
            $fileName = $fileUploader->upload( $cours->getMedia()->mp4FileUpload, 'media/courses' );
            $cours->getMedia()->setMp4File( $fileName );
        }
    }

    #[ Route( '{slug}/published', name: 'app_instructor_courses_published' ) ]

    public function publishedCourse( Cours $cours, Request $request, EnseignantRepository $enseignantRepository, CoursRepository $coursRepository ): Response
 {
        $enseignant = $enseignantRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $enseignant === null || !$enseignant->isIsCertified()  || $enseignant->getId() !== $cours->getEnseignant()->getId() ) {
            throw $this->createAccessDeniedException();
        }

        if ( !$this->isCsrfTokenValid( 'published' . $cours->getId(), $request->query->get( '_token' ) ) ) {
            throw $this->createAccessDeniedException();
        }

        $isValid = true;

        $writtenLessons = 0;
        foreach ( $cours->getChapitres() as $chap ) {
            $writtenLessons += count( $chap->getLessons() );
        }

        if ( $writtenLessons != $cours->getNumberOfLessons() ) {
            $isValid = false;
            $this->addFlash( 'danger', 'Action Impossible !!!<br>Le nombre de leçons rédigées ne correspond pas au nombre que vous avez declaré dans la configuration du cours' );
        }

        $finalEvalQuiz = $this->entityManager->getRepository( Quiz::class )->findBy( [ 'chapitre' => null
        , 'cours'=>$cours ] );

        if ( count( $finalEvalQuiz ) < 5 ) {
            $isValid = false;
            $this->addFlash( 'danger', 'Action impossible !!!<br>Vous avez ommis de mettre une évaluation de fin pour votre cours. Vous devez mettre au moins 5 questions' );
        }
        foreach ( $cours->getChapitres() as $chap ) {
            if ( count( $chap->getQuizzes() ) < 5 ) {
                $this->addFlash( 'danger', 'Action impossible !!!<br>Le chapitre '. $chap->getTitle(). " n'a pas d'évaluation. Vous devez mettre au moins 5 questions." );
                $isValid = false;
            }
        }

        if ( $isValid ) {
            $cours->setIsPublished( true )->setIsRejected( false );
            $coursRepository->save( $cours, true );
        }

        return $this->redirectToRoute( 'app_instructor_courses', [ 'page' => $request->query->getInt( 'page', 1 ) ] );
    }
}