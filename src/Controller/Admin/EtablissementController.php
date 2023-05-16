<?php

namespace App\Controller\Admin;

use App\Entity\Etablissement;
use App\Form\EtablissementType;
use App\Repository\EtablissementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/schools')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class EtablissementController extends AbstractController
{
    private const ACTIVE_PAGE = 'etc';

    #[Route('/', name: 'app_admin_etablissement_index', methods: ['GET'])]
    public function index(EtablissementRepository $etablissementRepository): Response
    {
        $etablissement = new Etablissement();
        $form = $this->createForm(EtablissementType::class, $etablissement, [
            'action' => $this->generateUrl('app_admin_etablissement_new')
        ]);

        return $this->render('admin/etablissement/index.html.twig', [
            'etablissements' => $etablissementRepository->findAll(),
            'etc' => self::ACTIVE_PAGE,
            'isInstitution' => true,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_admin_etablissement_new', methods: ['POST'])]
    public function new(Request $request, EtablissementRepository $etablissementRepository): Response
    {
        $etablissement = new Etablissement();
        $form = $this->createForm(EtablissementType::class, $etablissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etablissementRepository->save($etablissement, true);
        }

        return $this->redirectToRoute('app_admin_etablissement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_etablissement_show', methods: ['GET'])]
    public function show(Etablissement $etablissement): Response
    {
        return $this->render('admin/etablissement/show.html.twig', [
            'etablissement' => $etablissement,
            'etc' => self::ACTIVE_PAGE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_etablissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etablissement $etablissement, EtablissementRepository $etablissementRepository): Response
    {
        $form = $this->createForm(EtablissementType::class, $etablissement, [
            'action' => $this->generateUrl('app_admin_etablissement_edit', ['id' => $etablissement->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etablissementRepository->save($etablissement, true);
        }
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/etablissement/edit.html.twig', [
                'etablissement' => $etablissement,
                'form' => $form->createView(),
                'etc' => self::ACTIVE_PAGE,
            ]);
        }
        
        return $this->redirectToRoute('app_admin_etablissement_index', [], Response::HTTP_SEE_OTHER);

        
    }

    #[Route('/{id}', name: 'app_admin_etablissement_delete', methods: ['POST'])]
    public function delete(Request $request, Etablissement $etablissement, EtablissementRepository $etablissementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etablissement->getId(), $request->request->get('_token'))) {
            $etablissementRepository->remove($etablissement, true);
        }

        return $this->redirectToRoute('app_admin_etablissement_index', [], Response::HTTP_SEE_OTHER);
    }
}
