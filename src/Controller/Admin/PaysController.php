<?php

namespace App\Controller\Admin;

use App\Entity\Pays;
use App\Form\PaysType;
use App\Repository\PaysRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/pays')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class PaysController extends AbstractController
{
    private const ACTIVE_PAGE = 'pac';

    #[Route('/', name: 'app_admin_pays_index', methods: ['GET'])]
    public function index(PaysRepository $paysRepository): Response
    {
        $pay = new Pays();
        $form = $this->createForm(PaysType::class, $pay, [
            'action' => $this->generateUrl('app_admin_pays_new')
        ]);

        return $this->render('admin/pays/index.html.twig', [
            'pays' => $paysRepository->findAll(),
            'pac' => self::ACTIVE_PAGE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_pays_new', methods: ['POST'])]
    public function new(Request $request, PaysRepository $paysRepository, SluggerInterface $slugger): Response
    {
        $pay = new Pays();
        $form = $this->createForm(PaysType::class, $pay, [
            'action' => $this->generateUrl('app_admin_pays_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pay->setSlug($slugger->slug($pay->getName()));
            $paysRepository->save($pay, true);
        }
        return $this->redirectToRoute('app_admin_pays_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_pays_show', methods: ['GET'])]
    public function show(Pays $pay): Response
    {
        return $this->render('admin/pays/show.html.twig', [
            'pay' => $pay,
            'pac' => self::ACTIVE_PAGE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_pays_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pays $pay, PaysRepository $paysRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PaysType::class, $pay, [
            'action' => $this->generateUrl('app_admin_pays_edit', ['id' => $pay->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pay->setSlug($slugger->slug($pay->getName()));
            $paysRepository->save($pay, true);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/pays/edit.html.twig', [
                'pay' => $pay,
                'form' => $form->createView(),
                'pac' => self::ACTIVE_PAGE,
            ]);
        }

        return $this->redirectToRoute('app_admin_pays_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/{id}', name: 'app_admin_pays_delete', methods: ['POST'])]
    public function delete(Request $request, Pays $pay, PaysRepository $paysRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pay->getId(), $request->request->get('_token'))) {
            $paysRepository->remove($pay, true);
        }

        return $this->redirectToRoute('app_admin_pays_index', [], Response::HTTP_SEE_OTHER);
    }
}
