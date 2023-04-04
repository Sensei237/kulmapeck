<?php

namespace App\Controller\Admin;

use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    // notifications: 1 = cours publié, 2 = demande enseignant, 3 = 
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(CoursRepository $coursRepository, EleveRepository $eleveRepository, NotificationRepository $notificationRepository, EnseignantRepository $enseignantRepository): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'dashboardController',
            'completedCourses' => $coursRepository->findBy(['isValidated' => true,]),
            'courseInProgress' => $coursRepository->findBy(['isValidated' => false]),
            'eleves' => $eleveRepository->findAll(),
            'notifications' => $notificationRepository->findBy(['isRead' => false, 'destinataire' => $this->getUser()], ['createdAt' => 'DESC'], 5),
            'enseignants' => $enseignantRepository->findBy(['isValidated' => true]),
            'topInstructors' => $enseignantRepository->findBy(['isValidated' => true], ['review' => 'DESC'], 5),
            'allNotifications' => $notificationRepository->findBy(['destinataire' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }
}
