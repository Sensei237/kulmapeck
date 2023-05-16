<?php

namespace App\Controller\Admin;

use App\Entity\TypeEnseignement;
use App\Form\TypeEnseignementType;
use App\Repository\TypeEnseignementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/type/enseignement')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class TypeEnseignementController extends AbstractController
{
    private const ACTIVE_PAGE = 'tec';

    #[Route('/', name: 'app_admin_type_enseignement_index', methods: ['GET'])]
    public function index(TypeEnseignementRepository $typeEnseignementRepository): Response
    {
        $typeEnseignement = new TypeEnseignement();
        $form = $this->createForm(TypeEnseignementType::class, $typeEnseignement, [
            'action' => $this->generateUrl('app_admin_type_enseignement_new'),
            'method' => 'POST'
        ]);

        return $this->render('admin/type_enseignement/index.html.twig', [
            'type_enseignements' => $typeEnseignementRepository->findAll(),
            'tec' => self::ACTIVE_PAGE,
            'isInstitution' => true,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/new', name: 'app_admin_type_enseignement_new', methods: ['POST'])]
    public function new(Request $request, TypeEnseignementRepository $typeEnseignementRepository, SluggerInterface $slugger): Response
    {
        $typeEnseignement = new TypeEnseignement();
        $form = $this->createForm(TypeEnseignementType::class, $typeEnseignement, [
            'action' => $this->generateUrl('app_admin_type_enseignement_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeEnseignement->setSlug($slugger->slug($typeEnseignement->getName()));
            $typeEnseignementRepository->save($typeEnseignement, true);
        }

        return $this->redirectToRoute('app_admin_type_enseignement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_type_enseignement_show', methods: ['GET'])]
    public function show(TypeEnseignement $typeEnseignement): Response
    {
        return $this->render('admin/type_enseignement/show.html.twig', [
            'type_enseignement' => $typeEnseignement,
            'tec' => self::ACTIVE_PAGE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_type_enseignement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeEnseignement $typeEnseignement, TypeEnseignementRepository $typeEnseignementRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TypeEnseignementType::class, $typeEnseignement, [
            'action' => $this->generateUrl('app_admin_type_enseignement_edit', ['id' => $typeEnseignement->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeEnseignement->setSlug($slugger->slug($typeEnseignement->getName()));
            $typeEnseignementRepository->save($typeEnseignement, true);

        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/type_enseignement/edit.html.twig', [
                'type_enseignement' => $typeEnseignement,
                'form' => $form->createView(),
                'tec' => self::ACTIVE_PAGE,
            ]);
        }

        return $this->redirectToRoute('app_admin_type_enseignement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_type_enseignement_delete', methods: ['POST'])]
    public function delete(Request $request, TypeEnseignement $typeEnseignement, TypeEnseignementRepository $typeEnseignementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeEnseignement->getId(), $request->request->get('_token'))) {
            $typeEnseignementRepository->remove($typeEnseignement, true);
        }

        return $this->redirectToRoute('app_admin_type_enseignement_index', [], Response::HTTP_SEE_OTHER);
    }
}
