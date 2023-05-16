<?php

namespace App\EventSubscriber;

use App\Repository\PersonneRepository;
use App\Repository\SiteSettingRepository;
use App\Repository\SocialSettingRepository;
use App\Repository\UserRepository;
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
        RequestStack $requestStack)
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
            }

            // On verifie si le site est en mode maintenance

            $session->set('siteSettings', $siteSettings);
            $session->set('socialsSettings', $socialsSettings);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
