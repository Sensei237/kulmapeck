<?php

namespace App\EventSubscriber;

use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private $userRepository;
    private $personneRepository;
    private $requestStack;

    public function __construct(UserRepository $userRepository, RequestStack $requestStack, PersonneRepository $personneRepository)
    {
        $this->userRepository = $userRepository;
        $this->personneRepository = $personneRepository;
         $this->requestStack = $requestStack;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        if (!in_array('application/json', $this->requestStack->getCurrentRequest()->getAcceptableContentTypes())) {
            $personne = $this->personneRepository->findOneBy(['utilisateur' => $event->getAuthenticationToken()->getUser()]);

            if ($personne) {
                $this->requestStack->getSession()->set('personne', $personne);
            }
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
        ];
    }
}
