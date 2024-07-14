<?php

namespace App\Controller\Admin;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\PersonneRepository;
use App\Service\PushNotificationService;
use App\Service\SendAllUsersEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use HTMLPurifier;
use HTMLPurifier_Config;

#[Route('/evaluation')]

class EvaluationController extends AbstractController
{
    private $sendAllUsersEmailService;

    public function __construct(SendAllUsersEmailService $sendAllUsersEmailService)
    {
        $this->sendAllUsersEmailService = $sendAllUsersEmailService;
    }

    #[Route('/', name: 'app_admin_evaluation_index', methods: ['GET'])]

    public function index(PersonneRepository $personneRepository, EvaluationRepository $evaluationRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/evaluation/index.html.twig', [
                'evaluations' => $evaluationRepository->findBy([], ['endAt' => 'DESC']),
                'isCourses' => true,
                'evc' => true,
            ]);
        } else {
            $personne = $personneRepository->findOneBy(['utilisateur' => $this->getUser()]);
            if (!$personne) {
                throw $this->createAccessDeniedException();
            }

            $enseignant = $personne->getUtilisateur()->getEnseignant();
            // Render a different template for other roles ( instructor, etc. )
            return $this->render('instructor/evaluation/index.html.twig', [
                'evaluations' => $evaluationRepository->findBy(['enseignant' => $enseignant], ['endAt' => 'DESC']),
                'isCourses' => true,
                'evc' => true,
            ]);
        }
    }

    #[Route('/new', name: 'app_admin_evaluation_new', methods: ['GET', 'POST'])]

    function new(
        PersonneRepository $personneRepository,
        Request $request,
        EvaluationRepository $evaluationRepository,
        SluggerInterface $sluggerInterface,
        PushNotificationService $pushNotificationService
    ): Response {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setSlug($sluggerInterface->slug($evaluation->getTitre()) . '-' . time());

            if ($this->isGranted('ROLE_INSTRUCTOR')) {

                $personne = $personneRepository->findOneBy(['utilisateur' => $this->getUser()]);
                if (!$personne) {
                    throw $this->createAccessDeniedException();
                }

                $enseignant = $personne->getUtilisateur()->getEnseignant();

                $evaluation->setEnseignant($enseignant);
            } else {
                // ici le super admin confirm l'evaluation programmed
                $evaluation->setIsPublished(true);
                $date = $form->get('startAt')->getData();
                $title = $form->get('titre')->getData() . " Evaluation Programmée le " . $date->format('Y-m-d H:i:s');
                $body = $form->get('description')->getData();

                $parsedContent = $this->parseHtmlBodyContent($body);
                $pushNotificationService->PushNotificationData($parsedContent, $title);

                $pushNotificationService->PushNotificationData($body, $title);
                $this->sendAllUsersEmailService->send($title, $body, null);
            }
            $evaluationRepository->save($evaluation, true);

            return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/evaluation/new.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ]);
        } else {
            return $this->render('instructor/evaluation/new.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ]);
        }
    }

    #[Route('/{id}', name: 'app_admin_evaluation_show', methods: ['GET'])]

    function show(Evaluation $evaluation, EvaluationResultatRepository $evaluationResultatRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/evaluation/show.html.twig', [
                'evaluation' => $evaluation,
                'isCourses' => true,
                'evc' => true,
                'resultats' => $evaluationResultatRepository->findBy(['evaluation' => $evaluation], ['noteObtenue' => 'DESC']),
            ]);
        } else {
            return $this->render('instructor/evaluation/show.html.twig', [
                'evaluation' => $evaluation,
                'isCourses' => true,
                'evc' => true,
                'resultats' => $evaluationResultatRepository->findBy(['evaluation' => $evaluation], ['noteObtenue' => 'DESC']),
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'app_admin_evaluation_edit', methods: ['GET', 'POST'])]

    function edit(
        Request $request,
        Evaluation $evaluation,
        EvaluationRepository
        $evaluationRepository,
        PushNotificationService $pushNotificationService
    ): Response {
        if ($evaluation->isIsPassed()) {
            return $this->createAccessDeniedException();
        }

        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $evaluation->setIsPublished(true);
                $date = $form->get('startAt')->getData();
                $dateEnd = $form->get('endAt')->getData();
                $matiere = $form->get('matiere')->getData()->getName();

                $title = $form->get('titre')->getData() . " Evaluation Programmée de " . $matiere . " du " .
                    $date->format('d/m/Y') . ' à ' . $date->format('H:i') . " au " .
                    $dateEnd->format('d/m/Y') . ' à ' . $dateEnd->format('H:i');
                $body = $title . "  " . $form->get('description')->getData() . ".<br><strong>NB: Veuillez penser à vous inscrire à la liste des participants au plus tard 1 heure avant l'heure de début</strong>";

                $parsedContent = $this->parseHtmlBodyContent($body);

                $pushNotificationService->PushNotificationData($parsedContent, $title);

                $evaluationRepository->save($evaluation, true);

                $this->sendAllUsersEmailService->send($title, $body, null);

                return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/evaluation/edit.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ]);
        } else {
            return $this->render('instructor/evaluation/edit.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ]);
        }
    }

    #[Route('/{id}', name: 'app_admin_evaluation_delete', methods: ['POST'])]

    function delete(Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository): Response
    {
        if ($evaluation->isIsPassed()) {
            return $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $evaluation->getId(), $request->request->get('_token'))) {
            $evaluationRepository->remove($evaluation, true);
        }

        return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }

    function parseHtmlBodyContent($html)
    {
        $crawler = new Crawler($html);
        $textContent = $crawler->text();
        $strippedText = strip_tags($textContent);

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        // Purify the HTML content to remove any malicious or unwanted tags
        $cleanHtmlContent = $purifier->purify($strippedText);

        return  $cleanHtmlContent;
    }
}
