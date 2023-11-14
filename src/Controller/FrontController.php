<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\SiteSetting;
use App\Form\ContactType;
use App\Repository\AbonnementItemRepository;
use App\Repository\AbonnementRepository;
use App\Repository\CategorieRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\FormationRepository;
use App\Repository\ReviewRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller contient toutes les methodes utiles pour le fonctionnement du site web
 */

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(Request $request, EleveRepository $eleveRepository, CategorieRepository $categorieRepository, CoursRepository $coursRepository, ReviewRepository $reviewRepository): Response
    {
        $categories = [];
        foreach ($categorieRepository->findBy(['isSubCategory'=>false,], [], 6) as $category) {
            $categories[] = [
                'category' => $category,
                'courses' => $coursRepository->findBy(['categorie' => $category, 'isValidated' => true], ['vues' => 'DESC'], 8)
            ];
        }

        return $this->render('front/home/index.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories,
            'trendingCourses' => $coursRepository->findBy(['isValidated' => true], ['isFree' => 'ASC', 'review' => 'DESC', 'createdAt' => 'DESC'], 6),
            'topReviews' => $reviewRepository->findBy([], ['rating' => 'DESC', 'createdAt' => 'DESC'], 2),
            'dailyStudents' => $eleveRepository->findBy([], ['joinAt' => 'DESC'], 4),
            'isHomePage' => true,
        ]);
    }

    #[Route('/about-us', name: 'app_front_about')]
    public function about(): Response 
    {
        return $this->render('front/about/index.html.twig', [
            'isAboutPage' => true,
        ]);
    }

    #[Route('/contact-us', name: 'app_front_contact')]
    public function contact(Request $request, MailerInterface $mailer, SessionInterface $session): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $siteSettings = $session->get('siteSettings');
            $supportEmail = 'no-reply@kulmapeck.com';
            if ($siteSettings instanceof SiteSetting) {
                $supportEmail = $siteSettings->getSupportEmail();
            }
            $email = (new TemplatedEmail())
                ->from(new Address($contactForm->get('email')->getData(), $contactForm->get('name')->getData()))
                ->to($supportEmail)
                ->subject("Kulmapeck contact message")
                ->htmlTemplate('front/contact/email.html.twig')
                ->context([
                    'message' => $contactForm->get('message')->getData()
                ])
            ;
            $mailer->send($email);

            $this->addFlash("messageSend", "Your message has been send");
            return $this->redirectToRoute('app_front_contact');
        }

        return $this->render('front/contact/index.html.twig', [
            'isContactPage' => true,
            'contactForm' => $contactForm->createView()
        ]);
    }

    #[Route('/header-categories', name: 'app_front_header_categories')]
    public function showHeaderCategories(CategorieRepository $categorieRepository): Response 
    {
        return $this->render('front/home/header/categories.html.twig', [
            'categories' => $categorieRepository->findBy(['isSubCategory' => false], ['name' => 'ASC'], 11)
        ]);
    }

    #[Route('/header-courses', name: 'app_front_header_courses_and_formations')]
    public function showHeaderCourses(CategorieRepository $categorieRepository, CoursRepository $coursRepository, FormationRepository $formationRepository): Response
    {
        return $this->render('front/home/header/courses.html.twig', [
            'categories' => $categorieRepository->findBy(['isSubCategory' => false], ['name' => 'ASC'], 12),
            'lastCourses' => $coursRepository->findBy(['isValidated' => true], ['createdAt' => 'DESC', 'vues' => 'DESC'], 8)
        ]);
    }

    #[Route('/subscriptions-plans', name: 'app_plan')]
    public function plan(Request $request, AbonnementItemRepository $abonnementItemRepository
    , EleveRepository $eleveRepository, AbonnementRepository $abonnementRepository) {

        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        if ($eleve && $eleve->isIsPremium()) {
            $this->addFlash('success', "Vous êtes déjà premium !");
            return $this->redirectToRoute('app_student_subscriptions');
        }

        return $this->render('front/plan/index.html.twig', [
            'plans' => $abonnementRepository->findBy([], ['montant' => 'ASC']),
            'abonnementItems' => $abonnementItemRepository->findAll(),
        ]);
    }

    #[Route('/become-teacher', name: 'app_become_teacher')]
    public function becomeTeacher(Request $request, MailerInterface $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $session = $request->getSession();
            $siteSettings = $session->get('siteSettings');
            $supportEmail = 'no-reply@kulmapeck.com';
            if ($siteSettings instanceof SiteSetting) {
                $supportEmail = $siteSettings->getSupportEmail();
            }
            $email = (new TemplatedEmail())
                ->from(new Address($contactForm->get('email')->getData(), $contactForm->get('name')->getData()))
                ->to($supportEmail)
                ->subject("Kulmapeck contact message")
                ->htmlTemplate('front/contact/email.html.twig')
                ->context([
                    'message' => $contactForm->get('message')->getData()
                ])
            ;
            $mailer->send($email);

            $this->addFlash("messageSend", "Your message has been send");
            return $this->redirectToRoute('app_front_contact');
        }
        return $this->render('front/about/become_teacher.html.twig', [
            'isAboutPage' => true,
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
