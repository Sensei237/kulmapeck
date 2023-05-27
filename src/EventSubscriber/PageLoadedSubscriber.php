<?php

namespace App\EventSubscriber;

use App\Repository\EvaluationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PageLoadedSubscriber implements EventSubscriberInterface
{

    public function __construct(private EvaluationRepository $evaluationRepository)
    {
        
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $passedEvals = $this->evaluationRepository->updatePassedDate(new \DateTime());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
