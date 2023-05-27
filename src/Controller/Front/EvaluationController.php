<?php

namespace App\Controller\Front;

use App\Entity\Eleve;
use App\Entity\Evaluation;
use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evaluations')]
class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_front_evaluation')]
    public function index(): Response
    {
        return $this->render('front/evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
        ]);
    }

    #[Route('/{slug}/s-inscrire', name: 'app_front_evaluation_inscription')]
    public function sinscrire(Request $request, Evaluation $evaluation, EleveRepository $eleveRepository): Response 
    {
        $user = $this->getUser();
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve === null) {
            throw $this->createAccessDeniedException();
        }

        if (!$eleve->isIsPremium()) {
            $this->addFlash('info', "Vous devez être premium pour passer les examens organisés");
            return $this->redirectToRoute('app_plan');
        }

        if ($eleve->getEvaluations()->contains($evaluation)) {
            throw $this->createAccessDeniedException("Vous êtes déjà inscris !");
        }

        $evaluation->addEleve($eleve);
        $eleve->addEvaluation($evaluation);
        $eleveRepository->save($eleve, true);

        $request->getSession()->set('annonce', null);
        $request->getSession()->set('hasAnnonces', false);
        $request->getSession()->set('showAnnonces', true);

        $this->addFlash('success', 'Vous avez souscris à participer au test de harmonisé. vérifiez dans votre tableau de bord !');

        return $this->redirect($request->server->getHeaders()['REFERER']);
    }

    #[Route('/{slug}/hide-annonce', name: 'app_front_evaluation_hide_annonce')]
    public function hideAnnonce(Request $request, Evaluation $evaluation): Response
    {
        $hideAnnonces = $request->getSession()->get('hideAnnonces', []);
        $hideAnnonces[] = $evaluation->getId();

        $request->getSession()->set('hideAnnonces', $hideAnnonces);

        $request->getSession()->set('annonce', null);
        $request->getSession()->set('hasAnnonces', false);
        $request->getSession()->set('showAnnonces', true);

        return $this->redirect($request->server->getHeaders()['REFERER']);
    }
}
