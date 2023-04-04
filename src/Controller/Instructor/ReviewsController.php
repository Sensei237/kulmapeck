<?php

namespace App\Controller\Instructor;

use App\Entity\Notification;
use App\Entity\Review;
use App\Repository\EnseignantRepository;
use App\Repository\NotificationRepository;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instructor')]
class ReviewsController extends AbstractController
{
    #[Route('/reviews', name: 'app_instructor_reviews')]
    public function index(Request $request, PaginatorInterface $paginatorInterface, ReviewRepository $reviewRepository, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        $reviews = $reviewRepository->findInstrutorReviews($enseignant);

        return $this->render('instructor/reviews/index.html.twig', [
            'reviews' => $paginatorInterface->paginate($reviews, $request->query->getInt('page', 1), 5),
            'isReview' => true,
            'currentPAge' => $request->query->getInt('page', 1),
        ]);
    }

    #[Route('/{id}/reply', name: 'app_instructor_reply_review', methods: ['POST'])]
    public function reply(Request $request, Review $review, NotificationRepository $notificationRepository): Response
    {
        if (!$this->isCsrfTokenValid('reply' . $review->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $notification = new Notification();
        $notification->setDestinataire($review->getEleve()->getUtilisateur());
        $notification->setIsRead(false);
        $notification->setType(0);
        $title = $review->getCours()->getEnseignant()->getUtilisateur()->getPersonne()->getNomComplet() .' a répondu à votre commentaire';
        $notification->setTitle($title);
        $notification->setContent($request->request->get('message'));

        $notificationRepository->save($notification, true);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['hasDone' => true]);
        }

        return $this->redirectToRoute('app_instructor_reviews', ['page' => $request->request->getInt('page', 1)]);
    }
}
