<?php

namespace App\Controller;

use App\Entity\PushNotification;
use App\Form\PushNotificationType;
use App\Repository\PushNotificationRepository;
use App\Service\PushNotificationService;
use App\Service\SendAllUsersEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[ Route( '/admin/push/notification' ) ]

class PushNotificationController extends AbstractController {
    private $sendAllUsersEmailService;

    public function __construct( SendAllUsersEmailService $sendAllUsersEmailService ) {
        $this->sendAllUsersEmailService = $sendAllUsersEmailService;

    }

    #[ Route( '/', name: 'app_push_notification_index', methods: [ 'GET' ] ) ]

    public function index( PushNotificationRepository $pushNotificationRepository ): Response {
        return $this->render( 'push_notification/index.html.twig', [
            'push_notifications' => $pushNotificationRepository->findBy(
                [],
                [ 'id' => 'DESC' ],
                50
            ),
        ] );
    }

    #[ Route( '/new', name: 'app_push_notification_new', methods: [ 'GET', 'POST' ] ) ]

    public function new( Request $request, EntityManagerInterface $entityManager
    , PushNotificationService $pushNotificationService ): Response {
        $pushNotification = new PushNotification();
        $form = $this->createForm( PushNotificationType::class, $pushNotification );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $title = $form->get( 'title' )->getData();
            $body = $form->get( 'body' )->getData();

            $entityManager->persist( $pushNotification );
            $entityManager->flush();

            $response =  $pushNotificationService->PushNotificationData( $body, $title );

            $this->sendAllUsersEmailService->send( $title, $body, null );

            return $this->redirectToRoute( 'app_push_notification_index', [ 'resp'=>$response ], Response::HTTP_SEE_OTHER );
        }

        return $this->render( 'push_notification/new.html.twig', [
            'push_notification' => $pushNotification,
            'form' => $form,
        ] );
    }

    #[ Route( '/{id}', name: 'app_push_notification_show', methods: [ 'GET' ] ) ]

    public function show( PushNotification $pushNotification ): Response {
        return $this->render( 'push_notification/show.html.twig', [
            'push_notification' => $pushNotification,
        ] );
    }

    #[ Route( '/{id}/edit', name: 'app_push_notification_edit', methods: [ 'GET', 'POST' ] ) ]

    public function edit( Request $request, PushNotification $pushNotification,
    EntityManagerInterface $entityManager,
    PushNotificationService $pushNotificationService ): Response {
        $form = $this->createForm( PushNotificationType::class, $pushNotification );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $title = $form->get( 'title' )->getData();
            $body = $form->get( 'body' )->getData();
            $entityManager->flush();

            $pushNotificationService->PushNotificationData( $body, $title );

            $this->sendAllUsersEmailService->send( $title, $body, null );

            return $this->redirectToRoute( 'app_push_notification_index', [], Response::HTTP_SEE_OTHER );
        }

        return $this->render( 'push_notification/edit.html.twig', [
            'push_notification' => $pushNotification,
            'form' => $form,
        ] );
    }

    #[ Route( '/{id}', name: 'app_push_notification_delete', methods: [ 'POST' ] ) ]

    public function delete( Request $request, PushNotification $pushNotification, EntityManagerInterface $entityManager ): Response {
        if ( $this->isCsrfTokenValid( 'delete'.$pushNotification->getId(), $request->request->get( '_token' ) ) ) {
            $entityManager->remove( $pushNotification );
            $entityManager->flush();
        }

        return $this->redirectToRoute( 'app_push_notification_index', [], Response::HTTP_SEE_OTHER );
    }
}
