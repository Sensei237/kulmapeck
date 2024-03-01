<?php

namespace App\Controller\Front;

use App\Entity\Categorie;
use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Forum;
use App\Entity\ForumMessage;
use App\Entity\Lecture;
use App\Entity\Lesson;
use App\Entity\LikeMessageForum;
use App\Entity\Membre;
use App\Entity\QuizLost;
use App\Entity\QuizResult;
use App\Entity\Review;
use App\Entity\Sujet;
use App\Form\CourseReviewType;
use App\Form\SujetType;
use App\Repository\CategorieRepository;
use App\Repository\ChapitreRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\FiliereRepository;
use App\Repository\ForumMessageRepository;
use App\Repository\ForumRepository;
use App\Repository\LectureRepository;
use App\Repository\LessonRepository;
use App\Repository\LikeMessageForumRepository;
use App\Repository\MembreRepository;
use App\Repository\PaymentRepository;
use App\Repository\QuizLostRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizResultRepository;
use App\Repository\ReviewRepository;
use App\Repository\SpecialiteRepository;
use App\Repository\SujetRepository;
use DateTimeImmutable;
use App\Service\PushNotificationService;
use App\Service\SendAllUsersEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursesController extends AbstractController
{
     private $sendAllUsersEmailService;

    public function __construct( SendAllUsersEmailService $sendAllUsersEmailService ) {
        $this->sendAllUsersEmailService = $sendAllUsersEmailService;

    }

    #[Route('/courses', name: 'app_front_courses')]
    #[Route('/courses/{slug}', name: 'app_front_category_courses', methods: ['GET'])]
    public function index(Categorie $categorie = null, Request $request, FiliereRepository $filiereRepository, SpecialiteRepository $specialiteRepository, ClasseRepository $classeRepository, CategorieRepository $categorieRepository, CoursRepository $coursRepository, PaginatorInterface $paginatorInterface): Response
    {
        $search = trim($request->query->get('search')) === '' ? null : $request->query->get('search');
        $searchCategory = trim($request->query->get('category')) === '' ? null : $request->query->get('category');
        $searchPrice = trim($request->query->get('price')) === '' ? null : $request->query->get('price');
        $searchLevel = trim($request->query->get('level')) === '' ? null : $request->query->get('level');
        $searchLanguage = trim($request->query->get('language')) === '' ? null : $request->query->get('language');
        $category = $categorieRepository->findOneBy(['slug' => $searchCategory]);
        $isFree = $searchPrice === null ? null : ($searchPrice === 'free' ? true : false);
        $searchFiliere = $filiereRepository->findOneBy(['slug' => $request->query->get('filiere')]);
        $searchSpecialite = $specialiteRepository->findOneBy(['slug' => $request->query->get('specialite')]);
        // dd($searchSpecialite);
        $searchClasse = $classeRepository->findOneBy(['slug' => $request->query->get('classe')]);
        if ($search != null || $searchCategory != null || $searchPrice !== null || $searchLevel !== null || $searchLanguage !== null || $searchFiliere !== null || $searchSpecialite !== null || $searchClasse !== null) {
            $courses = $coursRepository->frontSearch($category, $search, $isFree, $searchLevel, $searchLanguage, $searchFiliere, $searchSpecialite, $searchClasse);
        } elseif ($categorie != null) {
            $courses = $coursRepository->findByCategory($categorie);
        } else {
            $courses = $coursRepository->findBy(['isValidated' => true]);
        }

        return $this->render('front/courses/index.html.twig', [
            'courses' => $paginatorInterface->paginate($courses, $request->query->getInt('page', 1), 12),
            'searchCategory' => $category,
            'category' => $categorie,
            'searchPrice' => $searchPrice,
            'searchLevel' => $searchLevel,
            'searchLanguage' => $searchLanguage,
            'categories' => $categorieRepository->findBy([], ['name' => 'ASC']),
            'search' => $search,
            'filieres' => $filiereRepository->findAll(),
            'searchFiliere' => $searchFiliere,
            'specialites' => $specialiteRepository->findBy([], ['filiere' => 'ASC']),
            'classes' => $classeRepository->findBy([], ['specialite' => 'ASC', 'name' => 'ASC']),
            'searchSpecialite' => $searchSpecialite,
            'searchClasse' => $searchClasse,
            'isCoursePage' => true,
        ]);
    }

    #[Route('/course/{slug}/review', name: 'app_front_course_add_review', methods: ['POST'])]
    public function newCourseReview(Cours $cours, Request $request, EleveRepository $eleveRepository, ReviewRepository $reviewRepository)
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($eleve === null) {
            throw $this->createAccessDeniedException("Cette opération est reservée aux élèves uniquement.");
        }

        $review = new Review();
        $formReview = $this->createForm(CourseReviewType::class, $review);
        $formReview->handleRequest($request);

        if ($formReview->isSubmitted() && $formReview->isValid()) {
            $cours->setReview($cours->getReview() + $review->getRating());
            $review->setEleve($eleve);
            $review->setCours($cours);
            $reviewRepository->save($review, true);
        }

        return $this->redirectToRoute('app_front_course_details', ['slug' => $cours->getSlug()]);
    }

    #[Route('/courses/{slug}/details', name: 'app_front_course_details', methods: ['GET'])]
    public function courseDetails(Cours $cours, CoursRepository $coursRepository, ReviewRepository $reviewRepository, MembreRepository $membreRepository, EleveRepository $eleveRepository, LectureRepository $lectureRepository, LessonRepository $lessonRepository)
    {
        $readLessons = $lessonRepository->findReadingLessons($cours);
        $lastReadLesson = null;
        $lecture = null;
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($eleve !== null && count($readLessons) > 0) {
            $lastReadLesson = $readLessons[count($readLessons) - 1];
            $lecture = $lectureRepository->findOneBy(['lesson' => $lastReadLesson, 'eleve' => $eleve]);
        }

        $review = new Review();
        $fromReview = $this->createForm(CourseReviewType::class, $review, [
            'action' => $this->generateUrl('app_front_course_add_review', ['slug' => $cours->getSlug()]),
        ]);

        $heIsMembre = false;
        $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($membre !== null && $membre->getForums()->contains($cours->getForum())) {
            $heIsMembre = true;
        }

        $sujet = new Sujet();
        $sujetForm = $this->createForm(SujetType::class, $sujet, [
            'action' => $heIsMembre ? $this->generateUrl('app_front_course_new_forum', ['id' => $membre->getId(), 'slug' => $cours->getSlug()])  : '',
        ]);

        $cours->setVues($cours->getVues() + 1);
        $coursRepository->save($cours, true);

        $view = 'front/courses/course_details.html.twig';
        if ($cours->getMedia()->getVideoUrl() === null && $cours->getMedia()->getMp4File() === null && $cours->getMedia()->getOggFile() === null && $cours->getMedia()->getWebMFile() === null) {
            $view = 'front/courses/course_details_text.html.twig';
        }

        return $this->render($view, [
            'course' => $cours,
            'fiveStarsReviews' => $reviewRepository->findBy(['cours' => $cours, 'rating' => 5]),
            'fourStarsReviews' => $reviewRepository->findBy(['cours' => $cours, 'rating' => 4]),
            'treeStarsReviews' => $reviewRepository->findBy(['cours' => $cours, 'rating' => 3]),
            'twoStarsReviews' => $reviewRepository->findBy(['cours' => $cours, 'rating' => 2]),
            'oneStarReviews' => $reviewRepository->findBy(['cours' => $cours, 'rating' => 1]),
            'nbReviews' => count($cours->getReviews()) == 0 ? 1 : count($cours->getReviews()),
            'lastReadLesson' => $lastReadLesson,
            'lecture' => $lecture,
            'fromReview' => $fromReview->createView(),
            'nbReviews' => count($cours->getReviews()) <= 0 ? 1 : count($cours->getReviews()),
            'sujetForm' => $sujetForm->createView(),
            'heIsMembre' => $heIsMembre,
            'membre' => $membre,
            'isReadLessonPage' => true,
            'topCourses' => $coursRepository->findBy(['isValidated' => true], ['review' => 'DESC', 'createdAt' => 'DESC'], 8),
            'isCoursePage' => true,
            'eleve' => $eleve,
            'lectures' => $eleve == null ? [] : $lectureRepository->findStudentCourseLectures($eleve, $cours, true),
            'lessons' => $lessonRepository->findCourseLessons($cours),
            'exist' => $eleve !== null && $eleve->getCours()->contains($cours)
        ]);
    }

    #[Route('/course/{id}/{slug}/forum', name: 'app_front_course_new_forum', methods: ['POST'])]
    #[ParamConverter('cours', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('membre', options: ['mapping' => ['id' => 'id']])]
    public function newForumSubjet(Membre $membre, Cours $cours,
     Request $request, SujetRepository $sujetRepository): Response
    {
        $sujet = new Sujet();
        $sujetForm = $this->createForm(SujetType::class, $sujet);
        $sujetForm->handleRequest($request);
        if ($sujetForm->isSubmitted() && $sujetForm->isValid()) {
            $formData = $sujetForm->getData();
            $content = $formData['content'];

            $sujet->setForum($cours->getForum());
            $sujet->setMembre($membre)
                ->setReference((time() + $membre->getId()) * 2);

              $sujetRepository->save($sujet, true);

            $title='Nouveu  sujet << '.$content.' >>';
            $body='Chere enseignant, le sujet : [ <strong>'.$content.'</strong> ] pour le cour de ' .$cours->getIntitule().' dont vous etes le guide vient de voir le jour  
            . Merci de bien consulter le soucis de cet apprenant ';
            
              $user= $cours->getEnseignant()->getUtilisateur();
              $this->sendAllUsersEmailService->sendManyRecipients( $title, $body,$user);

        }

        return $this->redirectToRoute('app_front_course_forum_index', ['slug' => $cours->getSlug()]);
    }

    #[Route('/course/{membreId}/{referenceSujet}/post-forum-message', name: 'app_front_course_new_forum_message', methods: ['POST'])]
    #[ParamConverter('sujet', options: ['mapping' => ['referenceSujet' => 'reference']])]
    #[ParamConverter('membre', options: ['mapping' => ['membreId' => 'id']])]
    public function addNewforumMessage(Membre $membre, Sujet $sujet,
     Request $request, ForumMessageRepository $forumMessageRepository,
        PushNotificationService $pushNotificationService,
    )
    {
        $user=$this->getUser();
        if ( in_array( 'ROLE_STUDENT', $user->getRoles() ) ) {
            if (!$membre->getForums()->contains($sujet->getForum())) {
              throw $this->createAccessDeniedException("You cannot post in this forum");
            }
       }
        $message = $request->request->get('message');
        if ($message === null) {
            throw $this->createNotFoundException("Comment connot be empty !");
        }
        
        if ($membre === 'adminer') {
                $membre = new Membre();
                $membre->setUtilisateur($this->getUser());
        }

        $forumMessage = new ForumMessage();
        $forumMessage->setContent($message)->setMembre($membre)->setSujet($sujet);
        $forumMessageRepository->save($forumMessage, true);

        $MessageMembers= $forumMessageRepository->findBy(['sujet'=>$sujet]);

        if($MessageMembers!==null){
           $isEleve=$membre->getUtilisateur()?->getEleve();
           $message=$isEleve==null ?' camarade ':' Enseignant Certifie Kulmapeck ';
           $sender=' du collaborateur  '.$message.' '.$membre->getUtilisateur()->getPersonne()->getPseudo();
            $title='Reponse au sujet '.$sujet->getContent();
            $body=' Un nouveau message a ete ajoute pour le sujet : '.$sujet->getContent().' priere de verifier si cela resoud votre preoccupation ';
            $bodyEmail='Pour le sujet : '.$sujet->getContent().'<br>, la  reponse   '.$sender.' est: <strong>'.$message.'</strong> . <br> 
            priere de verifier si cela resoud votre preoccupation et veuilez marquer resolut si vous etes satisfait . <br> Cordialement';

            foreach($MessageMembers as $member){
              $user= $member->getMembre()->getUtilisateur();
              $userDeviceToken = $member->getMembre()->getUtilisateur()?->getDevices()[0]->getToken();
              $pushNotificationService->PushNotificationData($body, $title,$userDeviceToken);
              $this->sendAllUsersEmailService->send( $title, $bodyEmail, $user );

            }
        }


        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['hasDone' => true]);
        }

        return $this->redirectToRoute('app_front_course_forum_subject_message', ['slug' => $sujet->getForum()->getCours()->getSlug(), 'reference' => $sujet->getReference()]);
    }

     #[Route('/course/{referenceSujet}/post-forum-message', name: 'app_front_course_new_forum_message_adminer', methods: ['POST'])]
    #[ParamConverter('sujet', options: ['mapping' => ['referenceSujet' => 'reference']])]
    public function addNewforumMessageAdminer(Membre $membre=null, Sujet $sujet,
     Request $request, ForumMessageRepository $forumMessageRepository,
        PushNotificationService $pushNotificationService,
    )
    {
        $user=$this->getUser();
        if ( in_array( 'ROLE_STUDENT', $user->getRoles() ) ) {
            if (!$membre->getForums()->contains($sujet->getForum())) {
              throw $this->createAccessDeniedException("You cannot post in this forum");
            }
       }
        $message = $request->request->get('message');
        if ($message === null) {
            throw $this->createNotFoundException("Comment connot be empty !");
        }
        
        if ($membre ===null) {
                $membre = new Membre();
                $membre->setUtilisateur($this->getUser());
        }

        $forumMessage = new ForumMessage();
        $forumMessage->setContent($message)->setMembre($membre)->setSujet($sujet);
        $forumMessageRepository->save($forumMessage, true);

        $MessageMembers= $forumMessageRepository->findBy(['sujet'=>$sujet]);

        if($MessageMembers!==null){
           $isEleve=$membre->getUtilisateur()?->getEleve();
           $message=$isEleve==null ?' camarade ':' Enseignant Certifie Kulmapeck ';
           $sender=' du collaborateur  '.$message.' '.$membre->getUtilisateur()->getPersonne()->getPseudo();
            $title='Reponse au sujet '.$sujet->getContent();
            $body=' Un nouveau message a ete ajoute pour le sujet : '.$sujet->getContent().' priere de verifier si cela resoud votre preoccupation ';
            $bodyEmail='Pour le sujet : '.$sujet->getContent().'<br>, la  reponse   '.$sender.' est: <strong>'.$message.'</strong> . <br> 
            priere de verifier si cela resoud votre preoccupation et veuilez marquer resolut si vous etes satisfait . <br> Cordialement';

            foreach($MessageMembers as $member){
              $user= $member->getMembre()->getUtilisateur();
              $userDeviceToken = $member->getMembre()->getUtilisateur()?->getDevices()[0]->getToken();
              $pushNotificationService->PushNotificationData($body, $title,$userDeviceToken);
              $this->sendAllUsersEmailService->send( $title, $bodyEmail, $user );

            }
        }


        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['hasDone' => true]);
        }

        return $this->redirectToRoute('app_front_course_forum_subject_message', ['slug' => $sujet->getForum()->getCours()->getSlug(), 'reference' => $sujet->getReference()]);
    }

    #[Route('/course/{id}/like-forum-message', name: 'app_course_like_forum_message', methods: ['POST'])]
    public function likeForumMessage(ForumMessage $forumMessage, Request $request, EntityManagerInterface $em, LikeMessageForumRepository $likeMessageForumRepository, ForumMessageRepository $forumMessageRepository, MembreRepository $membreRepository): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw $this->createAccessDeniedException("Vous n'êtes pas connecté");
        }

        $membre = $membreRepository->findOneBy(['user' => $user]);
        if ($membre === null || !$membre->getForums()->contains($forumMessage->getSujet()->getForum())) {
            throw $this->createAccessDeniedException("Action impossible !");
        }

        if ($likeMessageForumRepository->findOneBy(['membre' => $membre, 'forumMessage' => $forumMessage]) === null) {
            $forumMessageRepository->save($forumMessage->setLikes($forumMessage->getLikes() + 1));
            $lmf = new LikeMessageForum();
            $lmf->setMembre($membre)->setForumMessage($forumMessage);
            $likeMessageForumRepository->save($lmf);
            $em->flush();
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('app_front_course_forum_subject_message', ['slug' => $forumMessage->getSujet()->getForum()->getCours()->getSlug(), 'reference' => $forumMessage->getSujet()->getReference()]);
        }

        return new JsonResponse(['hasDone' => true, 'likes' => $forumMessage->getLikes()]);
    }

    #[Route('/course/reply-forum-message', name: 'app_course_reply_forum_message', methods: ['POST'])]
    #[ParamConverter('forumMessage', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('membre', options: ['mapping' => ['membreId' => 'id']])]
    public function replyForumMessage(Request $request, ForumMessageRepository $forumMessageRepository, MembreRepository $membreRepository)
    {
        $forumMessage = $forumMessageRepository->findOneBy(['id' => $request->request->get('reply')]);
        $membre = $membreRepository->findOneBy(['id' => $request->request->get('membre')]);
        if ($forumMessage === null || $membre === null || !$membre->getForums()->contains($forumMessage->getSujet()->getForum())) {
            throw $this->createAccessDeniedException("You cannot post in this forum");
        }

        $replyMessage = $request->request->get('replyMessage');
        if ($replyMessage === null) {
            throw $this->createNotFoundException("Comment connot be empty !");
        }

        $replyForumMessage = new ForumMessage();
        $replyForumMessage->setContent($replyMessage)->setMembre($membre)->setSujet($forumMessage->getSujet())->setIsAnswer(true)->setForumMessage($forumMessage);
        $forumMessageRepository->save($replyForumMessage, true);

        

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('app_front_course_forum_subject_message', ['slug' => $replyForumMessage->getSujet()->getForum()->getCours()->getSlug(), 'reference' => $replyForumMessage->getSujet()->getReference()]);
        }

        return new JsonResponse(['hasDone' => true, 'likes' => $replyForumMessage->getLikes()]);
    }

    #[Route('/course/{id}/mark-solve', name: 'app_course_solve_forum_message', methods: ['GET'])]
    public function sujetResolu(ForumMessage $forumMessage,
        PushNotificationService $pushNotificationService, ForumMessageRepository $forumMessageRepository)
    {
        $forumMessage->setIsResponse(true);
        $forumMessage->getSujet()->setIsSolved(true);

        $forumMessageRepository->save($forumMessage, true);

         $MessageMembers= $forumMessageRepository->findBy(['sujet'=>$forumMessage->getSujet()]);

        if($MessageMembers!==null){

            $title='Resolution du sujet << '.$forumMessage->getSujet()->getContent().' >>';
            $body='Le sujet : [ '.$forumMessage->getSujet()->getContent().' ] a ete resolu avec succes . Merci de nous faire confiance ';
            
            foreach($MessageMembers as $member){
              $user= $member->getMembre()->getUtilisateur();
              $userDeviceToken = $member->getMembre()->getUtilisateur()?->getDevices()[0]->getToken();
        
              $pushNotificationService->PushNotificationData($body, $title,$userDeviceToken);
              $this->sendAllUsersEmailService->send( $title, $body, $user );

            }
        }

        return $this->redirectToRoute('app_front_course_forum_index', ['slug' => $forumMessage->getSujet()->getForum()->getCours()->getSlug()]);
    }

    /**
     * Dans cette fonction nous allons étudier les différentes contraintes pour commencer un cours
     */
    #[Route('/course/{slug}/start', name: 'app_front_course_start', methods: ['GET'])]
    public function startCourse(Cours $course, Request $request, PaymentRepository $paymentRepository, MembreRepository $membreRepository, ForumRepository $forumRepository, EleveRepository $eleveRepository, LectureRepository $lectureRepository, EntityManagerInterface $entityManagerInterface)
    {
        // La fonction nécessite que l'on soit connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        // L'enseignant et l'admin peuvent lire gratuitement mais pas l'élève
        if ($this->isGranted('ROLE_STUDENT')) {
            // Si le cours est gratuit on l'ajoute dans la liste des cours de l'élève dans le cas contraire on verifie s'il a 
            // un compte premium si c'est le cas on ajoute le cours dans sa liste de cours dans le cas contraire il doit soit payer 

            if ($eleve === null) {
                throw $this->createAccessDeniedException();
            }

            // On verifie si l'élève n'a pas déjà ce cours dans sa liste des cours
            // le cours soit souscrire a un compte premium

            if (!$course->isIsFree() && (!$eleve->isIsPremium() && (!$paymentRepository->findOneBy(['eleve' => $eleve, 'cours' => $course, 'isExpired' => false])))) {

                return $this->redirectToRoute('app_front_payment_buy_course', ['slug' => $course->getSlug()]);
            }
            if (!$eleve->getCours()->contains($course)) {

                if ($course->isIsFree() || $eleve->isIsPremium()) {
                    $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
                    if ($membre === null) {
                        $membre = new Membre();
                        $membre->setUtilisateur($this->getUser());
                    }
                    if (!$course->getForum()) {
                        $forum = new Forum();
                        $forum->setCours($course);
                        $forumRepository->save($forum, true);
                        $course->setForum($forum);
                    }
                    $course->getForum()->addMembre($membre);
                    // On ajoute le cours dans sa liste
                    $eleve->addCour($course);
                    $membreRepository->save($membre, true);
                } else {
                    // On lui demande de soit ajouter payer le cours soit devenir premium
                    return $this->redirectToRoute('app_front_payment_buy_course', ['slug' => $course->getSlug()]);
                }
            } else {
                if (!$course->isIsFree() && (!$eleve->isIsPremium() && (!$paymentRepository->findOneBy(['eleve' => $eleve, 'cours' => $course, 'isExpired' => false])))) {

                    return $this->redirectToRoute('app_front_payment_buy_course', ['slug' => $course->getSlug()]);
                }
            }
        }
        $chapitre = $course->getChapitres()[0];
        $lesson = null;
        if ($eleve !== null) {
            $lecture = $lectureRepository->findStudentLastLecture($eleve, $course);
            if ($lecture) {
                $lesson = $lecture->getLesson();
            }
        }
        if ($lesson ===  null) {
            $lesson = $chapitre->getLessons()[0];
        }

        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_front_read_lesson', ['slug' => $lesson->getSlug()]);
    }

    #[Route('/course/{slug}/lesson', name: 'app_front_read_lesson', methods: ['GET'])]
    public function readLesson(Lesson $lesson, Request $request, PaymentRepository $paymentRepository, MembreRepository $membreRepository, EleveRepository $eleveRepository, EnseignantRepository $enseignantRepository, LectureRepository $lectureRepository)
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($this->isGranted('ROLE_STUDENT')) {
            // Si le cours est gratuit on l'ajoute dans la liste des cours de l'élève dans le cas contraire on verifie s'il a 
            // un compte premium si c'est le cas on ajoute le cours dans sa liste de cours dans le cas contraire il doit soit payer 

            if ($eleve === null) {
                throw $this->createAccessDeniedException();
            }

            // On verifie si l'élève n'a pas déjà ce cours dans sa liste des cours
            // le cours soit souscrire a un compte premium
            if (!$eleve->getCours()->contains($lesson->getChapitre()->getCours())) {
                return $this->redirectToRoute('app_front_course_start', ['slug' => $lesson->getChapitre()->getCours()->getSlug()]);
            }
            // else {
            //     if (!$eleve->isIsPremium() || !$paymentRepository->findOneBy(['eleve' => $eleve, 'cours' => $lesson->getChapitre()->getCours(), 'isExpired' => false])) {
            //         return $this->redirectToRoute('app_front_payment_buy_course', ['slug' => $lesson->getChapitre()->getCours()->getSlug()]);
            //     }
            // }
        } else {
            $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
            if (!$this->isGranted('ROLE_COURSE_MANAGER') && ($enseignant !== null && !$enseignant->getCours()->contains($lesson->getChapitre()->getCours()))) {
                throw $this->createAccessDeniedException();
            }
        }

        if ($lesson->getVideoLink() !== null && $request->query->get('view') === null) {
            return $this->redirectToRoute('app_front_read_lesson', ['slug' => $lesson->getSlug(), 'view' => 'video_player']);
        }

        $heIsMembre = false;
        $cours = $lesson->getChapitre()->getCours();
        $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($membre !== null && $membre->getForums()->contains($cours->getForum())) {
            $heIsMembre = true;
        }

        $sujet = new Sujet();
        $sujetForm = $this->createForm(SujetType::class, $sujet, [
            'action' => $heIsMembre ? $this->generateUrl('app_front_course_new_forum', ['id' => $membre->getId(), 'slug' => $cours->getSlug()])  : '',
        ]);

        // La fonction nécessite que l'on soit connecté

        $lecture = null;
        if ($eleve !== null) {
            $lecture = $lectureRepository->findOneBy(['lesson' => $lesson, 'eleve' => $eleve]);
            if ($lecture === null) {
                $lecture = new Lecture();
                $lecture->setReference($lesson->getId() + time())->setEleve($eleve)->setLesson($lesson)->setIsFinished(false)->setStartAt(new \DateTimeImmutable());
                $lectureRepository->save($lecture, true);
            }
        }

        $view = 'front/courses/read.html.twig';
        if ($lesson->getVideoLink() !== null && $request->query->get('view') === 'video_player') {
            $view = 'front/courses/video_player.html.twig';
        }

        return $this->render($view, [
            'lesson' => $lesson,
            'lecture' => $lecture,
            'heIsMembre' => $heIsMembre,
            'course' => $cours,
            'sujetForm' => $sujetForm,
            'nbReviews' => count($cours->getReviews()) <= 0 ? 1 : count($cours->getReviews()),
            'isReadLessonPage' => true,
            'membre' => $membre,
            'isCoursePage' => true,
            'eleve' => $eleve,
            'finishedLectures' => $lectureRepository->findBy(['eleve' => $eleve, 'isFinished' => true]),
        ]);
    }

    #[Route('/course/{reference}/lecture/finished', name: 'app_front_course_lecture_finished', methods: ['GET'])]
    public function lectureIsFinished(Lecture $lecture, LectureRepository $lectureRepository, LessonRepository $lessonRepository)
    {
        $lecture->setIsFinished(true)->setEndAt(new \DateTimeImmutable());
        $lectureRepository->save($lecture, true);

        $lesson = $lessonRepository->findOneBy(['chapitre' => $lecture->getLesson()->getChapitre(), 'numero' => $lecture->getLesson()->getNumero() + 1]);
        if ($lesson === null) {
            return $this->redirectToRoute('app_front_course_chapitre_quizzes', ['slugCours' => $lecture->getLesson()->getChapitre()->getCours()->getSlug(), 'slugChapitre' => $lecture->getLesson()->getChapitre()->getSlug()]);
        }

        return $this->redirectToRoute('app_front_read_lesson', ['slug' => $lesson->getSlug()]);
    }

    #[Route('/course/{slugCours}/{slugChapitre}/quizzes', name: 'app_front_course_chapitre_quizzes', methods: ['GET', 'POST'])]
    #[Route('/course/{slugCours}/quizzes', name: 'app_front_course_course_quizzes', methods: ['GET', 'POST'])]
    #[ParamConverter('cours', options: ['mapping' => ['slugCours' => 'slug']])]
    #[ParamConverter('chapitre', options: ['mapping' => ['slugChapitre' => 'slug']])]
    public function showQuizzes(Cours $cours, Chapitre $chapitre = null, PaymentRepository $paymentRepository, ChapitreRepository $chapitreRepository, QuizLostRepository $quizLostRepository, Request $request, EntityManagerInterface $entityManager, LectureRepository $lectureRepository, QuizResultRepository $quizResultRepository, EleveRepository $eleveRepository, QuizRepository $quizRepository)
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        if ($this->isGranted('ROLE_STUDENT')) {
            // Si le cours est gratuit on l'ajoute dans la liste des cours de l'élève dans le cas contraire on verifie s'il a 
            // un compte premium si c'est le cas on ajoute le cours dans sa liste de cours dans le cas contraire il doit soit payer 

            if ($eleve === null) {
                throw $this->createAccessDeniedException();
            }

            // On verifie si l'élève n'a pas déjà ce cours dans sa liste des cours
            // le cours soit souscrire a un compte premium
            if (!$eleve->getCours()->contains($cours)) {
                return $this->redirectToRoute('app_front_course_start', ['slug' => $cours->getSlug()]);
            } else {
                if (!$cours->isIsFree() && (!$eleve->isIsPremium() && ($paymentRepository->findOneBy(['eleve' => $eleve, 'cours' => $cours, 'isExpired' => false]) !== null))) {
                    return $this->redirectToRoute('app_front_payment_buy_course', ['slug' => $cours->getSlug()]);
                }
            }
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
            $lecture->setCours($cours)->setEleve($eleve)->setIsFinished(false)->setReference(time() + $eleve->getId())->setStartAt(new \DateTimeImmutable());
            $lectureRepository->save($lecture, true);
            $quizLost = $quizLostRepository->findOneBy(['cours' => $cours, 'eleve' => $eleve]);
        }

        $quizzesResults = [];

        if ($request->request->get('submit') !== null && $this->isCsrfTokenValid('postquiz' . $cours->getId(), $request->request->get('_token'))) {

            $data = $request->request->getIterator();
            $noteQuiz = 0;
            // dd($data['quizzes']);
            $quizzes = $data['quizzes'];
            foreach ($quizzes as $quizze) {
                $quizId = $quizze['id'];
                $results = $quizze['reponses'];
                $quiz = $quizRepository->find($quizId);
                if ($quiz === null) {
                    throw $this->createAccessDeniedException("Corrupted data send");
                }

                $isCorrect = false;
                $note = 0;
                // dump($quiz->getPropositionJuste());

                if ($results == $quiz->getPropositionJuste()) {
                    $isCorrect = true;
                    $note = 20 / count($chapitre->getQuizzes());
                    $noteQuiz += $note;
                    // dd($results);
                }

                if ($eleve !== null) {
                    $quizResult = $quizResultRepository->findOneBy(['quiz' => $quiz, 'eleve' => $eleve,]);
                    if ($quizResult === null) {
                        $quizResult = new QuizResult();
                    }
                    $quizResult->setEleve($eleve)->setQuiz($quiz)->setIsCorrect($isCorrect)->setResult($results)->setNote($note)->setUpdatedAt(new \DateTimeImmutable());
                    $quizResultRepository->save($quizResult);
                } else {
                    $quizResult = new QuizResult();
                    $quizResult->setQuiz($quiz)->setIsCorrect($isCorrect)->setResult($results)->setNote($note)->setUpdatedAt(new \DateTimeImmutable());
                    $quizzesResults[] = $quizResult;
                }

                // dump($quiz);dump($quizResult);dump($note);
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
                        ->setNextAt(new DateTimeImmutable(date('Y-m-d H:i:s', strtotime('+10 second'))))
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

            // dump("Note: ".$noteQuiz);dd($data['quizzes']);

            $entityManager->flush();

            if ($eleve) {
                if ($chapitre) {
                    return $this->redirectToRoute('app_front_course_chapitre_quizzes', ['slugCours' => $cours->getSlug(), 'slugChapitre' => $chapitre->getSlug()]);
                }
                return $this->redirectToRoute('app_front_course_course_quizzes', ['slugCours' => $cours->getSlug()]);
            }
        }

        $showCorrection = false;
        $currentDate = new \DateTimeImmutable();
        $nextQuizAt = null;
        if (($lecture && $lecture->isIsFinished()) || $quizLost && $currentDate->getTimestamp() < $quizLost->getNextAt()->getTimestamp()) {
            $showCorrection = true;
            $quizzesResults = $quizResultRepository->findStudentQuizResultsByCourseOrChapter($eleve, $cours, $chapitre);
            if ($quizLost && !$quizLost->isIsOk()) {
                $nextQuizAt = $quizLost->getNextAt();
            }
        }

        return $this->render('front/courses/quiz.html.twig', [
            'chapitre' => $chapitre,
            'quizzes' => $chapitre !== null ? $chapitre->getQuizzes() : $quizRepository->findBy(['cours' => $cours, 'chapitre' => null]),
            'cours' => $cours,
            'submitUri' => $chapitre === null ? $this->generateUrl('app_front_course_course_quizzes', ['slugCours' => $cours->getSlug()]) : $this->generateUrl('app_front_course_chapitre_quizzes', ['slugCours' => $cours->getSlug(), 'slugChapitre' => $chapitre->getSlug()]),
            'isCorrectionMode' => $showCorrection,
            'lecture' => $lecture,
            'quizzesResults' => $quizzesResults,
            'nextQuizAt' => $nextQuizAt,
        ]);
    }
}
