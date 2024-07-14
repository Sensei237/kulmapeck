<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MaintenanceListener
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $maintenanceMode = $this->parameterBag->get('MAINTENANCE_MODE');

        if ($maintenanceMode) {
            $response = new Response(
                $this->parameterBag->get('twig')->render('maintenance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            );

            $event->setResponse($response);
        }
    }
}