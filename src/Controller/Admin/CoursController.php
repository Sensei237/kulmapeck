<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use App\Entity\Forum;
use App\Entity\Notification;
use App\Entity\Review;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use App\Repository\NotificationRepository;
use App\Repository\NotificationTemplateRepository;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/cours')]
#[Security("is_granted('ROLE_COURSE_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_admin_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($request->get('search') !== null) {
            $courses = $coursRepository->searchByAdmin($request->query->get('search'));
        } elseif ($this->isGranted("ROLE_CHEF_DEPARTEMENT", $this->getUser())) {
            $courses = $coursRepository->findForChefDepartement($this->getUser());
        } 
        else {
            switch ($request->query->get('filter')) {
                case 'free':
                    $courses = $coursRepository->findBy(['isFree' => true], ['isPublished' => 'ASC', 'createdAt' => 'DESC']);
                    break;
                case 'newest':
                    $courses = $coursRepository->findBy(['isPublished' => true,], ['createdAt' => 'DESC']);
                    break;
                case 'premium':
                    $courses = $coursRepository->findBy(['isFree' => false], ['isPublished' => 'ASC', 'createdAt' => 'DESC']);
                    break;
                case 'oldest':
                    $courses = $coursRepository->findBy(['isPublished' => true], ['createdAt' => 'ASC']);
                    break;
                case 'accepted':
                    $courses = $coursRepository->findBy(['isValidated' => true], ['createdAt' => 'DESC']);
                    break;
                case 'rejected':
                    $courses = $coursRepository->findBy(['isRejected' => true], ['createdAt' => 'DESC']);
                default:
                    $courses = $coursRepository->findAll();
                    break;
            }
        }

        return $this->render('admin/cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
            'activatedCourses' => $coursRepository->findBy(['isValidated' => true]),
            'pendingCourses' => $coursRepository->findBy(['isValidated' => false, 'isPublished' => true]),
            'courses' => $paginatorInterface->paginate($courses, $request->query->getInt('page', 1), 10),
            'coc' => true,
            'isCourses' => true,
            'filter' => $request->query->get('filter'),
            'search' => $request->query->get('search')
        ]);
    }

    #[Route('/new', name: 'app_admin_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoursRepository $coursRepository, SluggerInterface $slugger): Response
    {
        throw $this->createAccessDeniedException();

        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cour->setSlug($slugger->slug(time() . '-' . $cour->getIntitule()));
            $coursRepository->save($cour, true);

            return $this->redirectToRoute('app_admin_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
            'coc' => true,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_cours_show', methods: ['GET'])]
    public function show(Cours $course, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        return $this->render('admin/cours/show.html.twig', [
            'course' => $course,
            'coc' => true,
            'eleves' => $paginatorInterface->paginate($course->getEleves(), $request->query->getInt('page', 1), 5),
            'reviews' => $paginatorInterface->paginate($course->getReviews(), $request->query->getInt('page', 1), 5),
            'isCourses' => true,
        ]);
    }

    #[Route('/{slug}/approve-course', name: 'app_admin_cours_approve', methods: ['GET'])]
    public function approveCourse(Cours $course, CoursRepository $coursRepository, NotificationRepository $notificationRepository, NotificationTemplateRepository $notificationTemplateRepository): Response
    {
        if (!$course->isIsPublished() || $course->isIsValidated()) {
            throw $this->createAccessDeniedException("Action Impossible");
        }

        $forum = $course->getForum();
        if ($forum === null) {
            $forum = new Forum();
            $course->setForum($forum);
        }

        $course->setIsValidated(true)->setIsPublished(true)->setPublishedAt(new \DateTimeImmutable());
        $coursRepository->save($course, true);

        $notfType = 2;
        $template = $notificationTemplateRepository->findOneBy(['notificationType' => $notfType]);
        if ($template) {
            $notification = new Notification();
            $notification->setDestinataire($course->getEnseignant()->getUtilisateur());
            $notification->setTitle($course->getIntitule());
            $content = str_replace("__title__", $course->getIntitule(), $template->getTemplate());
            $notification->setContent($content);
            $notification->setType($notfType);

            $notificationRepository->save($notification, true);
        }

        return $this->redirectToRoute('app_admin_cours_show', ['slug' => $course->getSlug()]);
    }

    #[Route('/{slug}/rejected-course', name: 'app_admin_cours_rejected', methods: ['POST'])]
    public function rejectedCourse(Cours $course, Request $request, NotificationRepository $notificationRepository, CoursRepository $coursRepository)
    {
        if (!$course->isIsPublished() || $course->isIsValidated()) {
            throw $this->createAccessDeniedException("Action Impossible");
        }

        $notfType = 3;
        $template = $request->request->get('message');
        if ($template) {
            $course->setIsRejected(true)->setIsPublished(false)->setIsValidated(false);
            $coursRepository->save($course, true);

            $notification = new Notification();
            $notification->setDestinataire($course->getEnseignant()->getUtilisateur());
            $notification->setTitle($course->getIntitule());
            $content = str_replace("__title__", $course->getIntitule(), $template);
            $notification->setContent($content);
            $notification->setType($notfType);

            $notificationRepository->save($notification, true);
        }

        return $this->redirectToRoute('app_admin_cours_show', ['slug' => $course->getSlug()]);

    }

    #[Route('/{slug}/send-for-update', name: 'app_admin_cours_send_to_update')]
    public function sendForUpdate(Cours $course, CoursRepository $coursRepository, NotificationRepository $notificationRepository) 
    {
        $course->setIsValidated(false)
            ->setIsPublished(false);
        $coursRepository->save($course, true);

        $notification = new Notification();
        $notification->setDestinataire($course->getEnseignant()->getUtilisateur());
        $notification->setTitle($course->getIntitule());
        $content = 'Le cours intitulé : ' . $course->getIntitule() . ', a été retourné pour correction';
        $notification->setContent($content);
        $notification->setType(3);

        $notificationRepository->save($notification, true);

        $this->addFlash('info', "Opération effectuée ! Le cours a été retiré du site pour une mise à jour.");

        return $this->redirectToRoute('app_admin_cours_show', ['slug' => $course->getSlug()]);
    }

    #[Route('/{id}/remove-review', name: 'app_admin_cours_remove_review', methods: ['POST'])]
    public function deleteReview(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('app_admin_cours_show', ['slug' => $review->getCours()->getSlug()]);
        }

        return new JsonResponse(['hasDone' => true]);
    }


    #[Route('/{id}/edit', name: 'app_admin_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, CoursRepository $coursRepository, SluggerInterface $slugger): Response
    {
        throw $this->createAccessDeniedException();

        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cour->setSlug($slugger->slug(time() . '-' . $cour->getIntitule()));
            $coursRepository->save($cour, true);

            return $this->redirectToRoute('app_admin_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
            'coc' => true,
            'isCourses' => true
        ]);
    }

    #[Route('/{id}', name: 'app_admin_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, CoursRepository $coursRepository): Response
    {
        throw $this->createAccessDeniedException();

        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $coursRepository->remove($cour, true);
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
