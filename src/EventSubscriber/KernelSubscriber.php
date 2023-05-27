<?php

namespace App\EventSubscriber;

use App\Entity\Eleve;
use App\Entity\Evaluation;
use App\Repository\EvaluationRepository;
use App\Repository\PersonneRepository;
use App\Repository\SiteSettingRepository;
use App\Repository\SocialSettingRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class KernelSubscriber implements EventSubscriberInterface
{
    private $requestStack;
    private $siteSettingRepository;
    private $urlGeneratorInterface;
    private $userRepo;
    private $personneRepo;

    public function __construct(PersonneRepository $personneRepo, 
        SiteSettingRepository $siteSettingRepository, 
        private SocialSettingRepository $socialSettingRepository,
        UserRepository $userRepo, 
        UrlGeneratorInterface $urlGeneratorInterface, 
        RequestStack $requestStack,
        private EvaluationRepository $evaluationRepository
        )
    {
        $this->siteSettingRepository = $siteSettingRepository;
        $this->requestStack = $requestStack;
        $this->urlGeneratorInterface = $urlGeneratorInterface;
        $this->userRepo = $userRepo;
        $this->personneRepo = $personneRepo;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!in_array('application/json', $event->getRequest()->getAcceptableContentTypes())) {
            $session = $this->requestStack->getSession();
            $personne = $session->get('personne');

            $siteSettings = $session->get('siteSettings', $this->siteSettingRepository->findOneBy([]));

            $socialsSettings = $session->get('socialsSettings', $this->socialSettingRepository->findAll());

            $session->set('siteSettings', $siteSettings);

            // dd($personne);
            if ($personne) {
                $personne = $this->personneRepo->findOneBy(['id' => $personne->getId()]);
                $user = $personne->getUtilisateur();
                if ($user->isIsBlocked() || !$user->isVerified()) {
                    $session->remove('personne');
                    $event->setResponse(new RedirectResponse($this->urlGeneratorInterface->generate('app_logout')));
                }

                $eleve = $user->getEleve();
                $hideAnnonces = $session->get('hideAnnonces', []);
                $sessionContainsAnnonces  = $session->get('hasAnnonces', false);
                $showAnnonces = $session->get('showAnnonces', true);
                // dump($sessionContainsAnnonces); dump($showAnnonces);dump($hideAnnonces);die;
                if ($eleve !== null && $eleve->getClasse() !== null && (!$sessionContainsAnnonces && $showAnnonces)) {
                    $classe = $eleve->getClasse();
                    $evaluations = $this->evaluationRepository->findSudentEvaluationsAnnonces($classe);
                    $annonces = null;
                    foreach ($evaluations as $evaluation) {
                        $nbJours = 2;
                        if (!$eleve->getEvaluations()->contains($evaluation) && $nbJours < 7 && !in_array($evaluation->getId(), $hideAnnonces)) {
                            $annonces = [
                                'evaluation' => [
                                    'titre' => $evaluation->getTitre(),
                                    'description' => $evaluation->getDescription(),
                                    'slug' => $evaluation->getSlug(),
                                    'startAt' => $evaluation->getStartAt(),
                                    'endAt' => $evaluation->getEndAt(),
                                    'duree' => $evaluation->getDuree(),
                                ],
                                'nombreInscris' => $evaluation->getEleves()->count(),
                                'matiere' => $evaluation->getMatiere()->getName(),
                            ];
                            $cmp = 1;
                            $annonces['eleves'] = [];
                            foreach ($evaluation->getEleves() as $e) {
                                $annonces['eleves'][] = $e->getUtilisateur()->getPersonne()->getAvatarPath(); 
                                $cmp++;
                                if ($cmp > 4) {
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    if (empty($annonces)) {
                        $session->set('showAnnonces', false);
                        $session->set('hasAnnonces', true);
                        $session->set('annonce', null);
                    } else {
                        $session->set('showAnnonces', true);
                        $session->set('hasAnnonces', true);
                        $session->set('annonce', $annonces);
                    }

                    $this->showAnnonceEvaluationEncours($evaluations, $eleve);
                }
            }
            // On verifie si le site est en mode maintenance

            $session->set('siteSettings', $siteSettings);
            $session->set('socialsSettings', $socialsSettings);

            $request = $event->getRequest();
            if ($locale = $request->attributes->get('_locale')) {
                $request->getSession()->set('_locale', $locale);
            }
            else {
                $request->setLocale($request->getSession()->get('_locale', 'fr'));
            }
        }
    } 
    
    /**
     * Undocumented function
     *
     * @param Evaluation[] $evaluations
     * @param Eleve $eleve
     * @return Evaluation|null
     */
    private function showAnnonceEvaluationEncours($evaluations, Eleve $eleve): ?Evaluation
    {
        $currentEvaluation = null;
        foreach ($evaluations as $evaluation) {
            $currentDateTime = new DateTime();
            if ($eleve->getEvaluations()->contains($evaluation) && $evaluation->getStartAt() <= $currentDateTime && $evaluation->getEndAt() > $currentDateTime->modify('+1 hour')) {
                $currentEvaluation = $evaluation;
                break;
            }
        }

        return $currentEvaluation;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
