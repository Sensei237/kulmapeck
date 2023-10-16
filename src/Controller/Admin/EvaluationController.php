<?php

namespace App\Controller\Admin;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\PersonneRepository;
use App\Service\PushNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ Route( '/evaluation' ) ]

class EvaluationController extends AbstractController
 {
    #[ Route( '/', name: 'app_admin_evaluation_index', methods: [ 'GET' ] ) ]

    public function index( PersonneRepository $personneRepository, EvaluationRepository $evaluationRepository ): Response
 {
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render( 'admin/evaluation/index.html.twig', [
                'evaluations' => $evaluationRepository->findBy( [], [ 'endAt' => 'DESC' ] ),
                'isCourses' => true,
                'evc' => true,
            ] );
        } else {
            $personne = $personneRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
            if ( !$personne ) {
                throw $this->createAccessDeniedException();
            }

            $enseignant = $personne->getUtilisateur()->getEnseignant();
            // Render a different template for other roles ( instructor, etc. )
            return $this->render( 'instructor/evaluation/index.html.twig', [
                'evaluations' => $evaluationRepository->findBy( [ 'enseignant'=>$enseignant ], [ 'endAt' => 'DESC' ] ),
                'isCourses' => true,
                'evc' => true,
            ] );
        }
    }

    #[ Route( '/new', name: 'app_admin_evaluation_new', methods: [ 'GET', 'POST' ] ) ]

    function new ( PersonneRepository $personneRepository, Request $request, 
    EvaluationRepository $evaluationRepository,
     SluggerInterface $sluggerInterface,PushNotificationService $pushNotificationService ): Response
 {
        $evaluation = new Evaluation();
        $form = $this->createForm( EvaluationType::class, $evaluation );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $evaluation->setSlug( $sluggerInterface->slug( $evaluation->getTitre() ) . '-' . time() );

            if ( $this->isGranted( 'ROLE_INSTRUCTOR' ) ) {

                $personne = $personneRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
                if ( !$personne ) {
                    throw $this->createAccessDeniedException();
                }

                $enseignant = $personne->getUtilisateur()->getEnseignant();

                $evaluation->setEnseignant( $enseignant );
            }else{
                // ici le super admin confirm l'evaluation programmed
                $evaluation->setIsPublished(true);
                $date = $form->get('startAt')->getData();
                $title = $form->get('titre')->getData()." Programmée le ".$date;
                $body = $form->get('description')->getData();
    
                $pushNotificationService->PushNotificationData($body,$title);
            }
            $evaluationRepository->save( $evaluation, true );

            return $this->redirectToRoute( 'app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER );
        }
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render( 'admin/evaluation/new.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ] );
        } else {
            return $this->render( 'instructor/evaluation/new.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ] );
        }

    }

    #[ Route( '/{id}', name: 'app_admin_evaluation_show', methods: [ 'GET' ] ) ]

    function show( Evaluation $evaluation, EvaluationResultatRepository $evaluationResultatRepository ): Response
 {
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render( 'admin/evaluation/show.html.twig', [
                'evaluation' => $evaluation,
                'isCourses' => true,
                'evc' => true,
                'resultats' => $evaluationResultatRepository->findBy( [ 'evaluation' => $evaluation ], [ 'noteObtenue' => 'DESC' ] ),
            ] );
        } else {
            return $this->render( 'instructor/evaluation/show.html.twig', [
                'evaluation' => $evaluation,
                'isCourses' => true,
                'evc' => true,
                'resultats' => $evaluationResultatRepository->findBy( [ 'evaluation' => $evaluation ], [ 'noteObtenue' => 'DESC' ] ),
            ] );
        }

    }

    #[ Route( '/{id}/edit', name: 'app_admin_evaluation_edit', methods: [ 'GET', 'POST' ] ) ]

    function edit( Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository ): Response
 {
        if ( $evaluation->isIsPassed() ) {
            return $this->createAccessDeniedException();
        }

        $form = $this->createForm( EvaluationType::class, $evaluation );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
                $evaluation->setIsPublished(true);
            }
            $evaluationRepository->save( $evaluation, true );

            return $this->redirectToRoute( 'app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER );
        }
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render( 'admin/evaluation/edit.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ] );
        } else {
            return $this->render( 'instructor/evaluation/edit.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
                'isCourses' => true,
                'evc' => true,
            ] );
        }

    }

    #[ Route( '/{id}', name: 'app_admin_evaluation_delete', methods: [ 'POST' ] ) ]

    function delete( Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository ): Response
 {
        if ( $evaluation->isIsPassed() ) {
            return $this->createAccessDeniedException();
        }

        if ( $this->isCsrfTokenValid( 'delete' . $evaluation->getId(), $request->request->get( '_token' ) ) ) {
            $evaluationRepository->remove( $evaluation, true );
        }

        return $this->redirectToRoute( 'app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER );
    }

}
