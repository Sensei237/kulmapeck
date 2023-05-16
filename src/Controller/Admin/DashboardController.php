<?php

namespace App\Controller\Admin;

use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\NotificationRepository;
use App\Repository\PaymentRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/dashboardEarningsData', name: 'app_admin_earnings', methods: ['GET'])]
    public function dashboardEarningsData(PaymentRepository $paymentRepository)
    {
        // On affiche le chiffre d'affaire des 12 derniers jours
        $data = [];
        $jours = [];
        
        for ($i=11; $i >= 0; $i--) { 
            $currentDay = new DateTime(date_format(new DateTimeImmutable(), 'Y-m-d'));
            $date = $currentDay->modify("-" . $i . " day");
            $jours[] = $date->format('d-m-Y');

            $dayStart = $date->format('Y-m-d 00:00:00');
            $dayEnd = $date->format('Y-m-d 23:59:59');

            $payments = $paymentRepository->findBetweenDates(new DateTime($dayStart), new DateTime($dayEnd));
            $totalP = 0;
            foreach ($payments as $p) {
                $totalP += $p->getAmount();
            }
            $data[] = $totalP;
        }

        return new JsonResponse([
            'data' => $data,
            'jours' => $jours
        ]);
    }

    #[Route('/traficSource', name: 'app_admin_trafic_sources', methods: ['GET'])]
    public function traficSource(CoursRepository $coursRepository) 
    {
        $cours = $coursRepository->findBy(['isValidated' => true], ['vues' => 'DESC'], 4);
        $data = [25, 25, 25, 25];
        $totalVues = 0;
        foreach ($cours as $c) {
            $totalVues += $c->getVues();
        }
        $totalVues = $totalVues == 0 ?? 1;
        $labels = ['Course 1', 'Course 2', 'Course 3', 'Course 4'];
        foreach ($cours as $key => $c) {
            $data[$key] = number_format(($c->getVues()/$totalVues) * 100, 2);
            $labels[$key] = $c->getIntitule();
        }
        return new JsonResponse(
            [
                'data' => $data,
                'labels' => $labels
            ]
        );
    }
}
