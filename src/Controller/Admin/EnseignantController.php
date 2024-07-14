<?php

namespace App\Controller\Admin;

use App\Entity\Enseignant;
use App\Entity\Notification;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use App\Repository\NotificationRepository;
use App\Repository\NotificationTemplateRepository;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[ Route( '/admin/instructors' ) ]
#[ Security( "is_granted('ROLE_TRAINERS_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page" ) ]

class EnseignantController extends AbstractController
 {
    #[ Route( '/', name: 'app_admin_enseignant_index', methods: [ 'GET' ] ) ]

    public function index( Request $request, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface ): Response
 {
        $enseignants = $enseignantRepository->findBy( [ 'isValidated' => true ], [ 'isValidated' => 'DESC' ] );

        return $this->render( 'admin/enseignant/index.html.twig', [
            'enseignants' => $paginatorInterface->paginate( $enseignants, $request->query->getInt( 'page', 1 ), 10 ),
            'isEnseignants' => true,
            'eni' => true,
            'search' => $request->get( 'search' ),
            'filter' => $request->get( 'filter' )
        ] );
    }

    #[ Route( '/requests', name: 'app_admin_enseignant_request', methods: [ 'GET' ] ) ]

    public function request( Request $request, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface ): Response
 {
        $filter = $request->get( 'filter' );
        if ( $filter !== null ) {
            if ( $filter === 'accepted' ) {
                $enseignants = $enseignantRepository->findBy( [ 'isValidated' => true ] );
            } elseif ( $filter === 'rejected' ) {
                $enseignants = $enseignantRepository->findBy( [ 'isRejected' => true ] );
            } else {
                $enseignants = [];
            }
        } else {
            $enseignants = $enseignantRepository->findAll();
        }

        return $this->render( 'admin/enseignant/request.html.twig', [
            'enseignants' => $paginatorInterface->paginate( $enseignants,
            $request->query->getInt( 'page', 1 ), 12 ),
            'isEnseignants' => true,
            'enr' => true,
            'search' => $request->get( 'search' ),
            'filter' => $request->get( 'filter' )
        ] );
    }

    #[ Route( '/request/{reference}/accept', name: 'app_admin_enseignant_accept_request', methods: [ 'GET' ] ) ]

    public function accepted( Enseignant $enseignant, Request $request, NotificationTemplateRepository $ntr, NotificationRepository $notificationRepository, EnseignantRepository $enseignantRepository )
 {
        $isCertified = strtolower( $request->query->get( 'type' ) ) === 'junior';
        $enseignant->getUtilisateur()->setIsBlocked( false );
        $enseignant->getUtilisateur()->setIsVerified( true );
        $enseignantRepository->save( $enseignant->setIsValidated( true )->setIsRejected( false )->setIsCertified( !$isCertified ), true );

        $notification = new Notification();
        $template = $ntr->findOneBy( [ 'type' => 7 ] );
        if ( $template ) {
            $content = $template->getTemplate();
        } else {
            $content = 'Kulmapck a approuvé votre candidature comme enseigant. Vous pouvez dès à présent rédiger des cours depuis votre espace personnel';
        }

        $notification->setTitle( 'Rejet de candidature' )
        ->setType( 7 )
        ->setDestinataire( $enseignant->getUtilisateur() )
        ->setContent( $content );
        $notificationRepository->save( $notification, true );

        return $this->redirectToRoute( 'app_admin_enseignant_request' );
    }

    #[ Route( '/request/{reference}/reject', name: 'app_admin_enseignant_reject_request', methods: [ 'POST' ] ) ]

    public function rejected( Enseignant $enseignant, Request $request, MailerInterface $mailer, NotificationTemplateRepository $ntr, NotificationRepository $notificationRepository, EnseignantRepository $enseignantRepository )
 {
        if ( !$this->isCsrfTokenValid( 'reject'.$enseignant->getId(), $request->request->get( '_token' ) ) ) {
            $this->addFlash( 'danger', 'Impossible de traiter ce formulaire' );
            return $this->redirectToRoute( 'app_admin_enseignant_request' );
        }

        $motif = $request->request->get( 'motif' );

        if ( !$motif || strlen( trim( $motif ) ) < 10 ) {
            $this->addFlash( 'info', 'Vous devez saisir un motif valide pour rejeter cette demande' );
            return $this->redirectToRoute( 'app_admin_enseignant_request' );
        }

        $enseignant->getUtilisateur()->setIsBlocked( false );
        $enseignant->setIsValidated( false )
        ->setIsRejected( true );
        $enseignantRepository->save( $enseignant, true );

        $email = ( new Email() )
        ->from( 'no-reply@kulmapeck.com' )
        ->to( $enseignant->getUtilisateur()->getEmail() )
        ->subject( 'Demande rejeter' )
        ->text( $motif )
        ->html( '<p>' . $motif .'</p>' );
        $mailer->send( $email );

        $notification = new Notification();
        $template = $ntr->findOneBy( [ 'type' => 6 ] );
        if ( $template ) {
            $content = $template->getTemplate();
        } else {
            $content = "Kulmapck n'a pas approuvé votre candidature comme enseigant. Veuillez prendre contact avec les dirigeants pour plus de détails sur ce rejet";
        }

        $notification->setTitle( 'Rejet de candidature' )
        ->setType( 6 )
        ->setDestinataire( $enseignant->getUtilisateur() )
        ->setContent( $content );
        $notificationRepository->save( $notification, true );

        return $this->redirectToRoute( 'app_admin_enseignant_request' );
    }

    #[ Route( '/new', name: 'app_admin_enseignant_new', methods: [ 'GET', 'POST' ] ) ]
    #[ Security( "is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page" ) ]

    public function new( Request $request, EnseignantRepository $enseignantRepository ): Response
 {
        throw $this->createAccessDeniedException();

        $enseignant = new Enseignant();
        $form = $this->createForm( EnseignantType::class, $enseignant );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $enseignantRepository->save( $enseignant, true );

            return $this->redirectToRoute( 'app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER );
        }

        return $this->render( 'admin/enseignant/new.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ] );
    }

    #[ Route( '/{reference}', name: 'app_admin_enseignant_show', methods: [ 'GET' ] ) ]

    public function show( Enseignant $enseignant, Request $request, PaginatorInterface $paginatorInterface, ReviewRepository $reviewRepository ): Response
 {
        return $this->render( 'admin/enseignant/show.html.twig', [
            'enseignant' => $enseignant,
            'courses' => $enseignant->getCours(),
            'reviews' => $reviewRepository->findInstrutorReviews( $enseignant ),
            'isEnseignants' => true,
            'eni' => true,
        ] );
    }

    #[ Route( '/{id}/edit', name: 'app_admin_enseignant_edit', methods: [ 'GET', 'POST' ] ) ]
    #[ Security( "is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page" ) ]

    public function edit( Request $request, Enseignant $enseignant, EnseignantRepository $enseignantRepository ): Response
 {
        throw $this->createAccessDeniedException();

        $form = $this->createForm( EnseignantType::class, $enseignant );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $enseignantRepository->save( $enseignant, true );

            return $this->redirectToRoute( 'app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER );
        }

        return $this->render( 'admin/enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form,
        ] );
    }

    #[ Route( '/{id}', name: 'app_admin_enseignant_delete', methods: [ 'POST' ] ) ]

    public function delete( Request $request, Enseignant $enseignant, EnseignantRepository $enseignantRepository ): Response
 {
        if ( $this->isCsrfTokenValid( 'delete'.$enseignant->getId(), $request->request->get( '_token' ) ) ) {
            $enseignantRepository->remove( $enseignant, true );
        }

        return $this->redirectToRoute( 'app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER );
    }
}
