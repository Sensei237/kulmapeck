<?php

namespace App\Controller\Admin;

use App\Entity\FAQ;
use App\Form\FAQType;
use App\Repository\FAQRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/f/a/q')]
#[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class FAQController extends AbstractController
{
    #[Route('/', name: 'app_admin_f_a_q_index', methods: ['GET'])]
    public function index(FAQRepository $fAQRepository): Response
    {
        return $this->render('admin/faq/index.html.twig', [
            'f_a_qs' => $fAQRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_f_a_q_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FAQRepository $fAQRepository): Response
    {
        $fAQ = new FAQ();
        $form = $this->createForm(FAQType::class, $fAQ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fAQRepository->save($fAQ, true);

            return $this->redirectToRoute('app_admin_f_a_q_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/faq/new.html.twig', [
            'f_a_q' => $fAQ,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_f_a_q_show', methods: ['GET'])]
    public function show(FAQ $fAQ): Response
    {
        return $this->render('admin/faq/show.html.twig', [
            'f_a_q' => $fAQ,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_f_a_q_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FAQ $fAQ, FAQRepository $fAQRepository): Response
    {
        $form = $this->createForm(FAQType::class, $fAQ);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fAQRepository->save($fAQ, true);

            return $this->redirectToRoute('app_admin_f_a_q_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/faq/edit.html.twig', [
            'f_a_q' => $fAQ,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_f_a_q_delete', methods: ['POST'])]
    public function delete(Request $request, FAQ $fAQ, FAQRepository $fAQRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fAQ->getId(), $request->request->get('_token'))) {
            $fAQRepository->remove($fAQ, true);
        }

        return $this->redirectToRoute('app_admin_f_a_q_index', [], Response::HTTP_SEE_OTHER);
    }
}
