<?php

namespace App\Controller\Admin;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use App\Repository\SujetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum')]
#[Security("is_granted('ROLE_ADMIN')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum_index', methods: ['GET'])]
    public function index(Request $request, ForumRepository $forumRepository, SujetRepository $sujetRepository, PaginatorInterface $paginatorInterface): Response
    {
        return $this->render('admin/forum/index.html.twig', [
            'forums' => $paginatorInterface->paginate($sujetRepository->findAll(), $request->query->getInt('page', 1), 10),
            'isForumController' => true,
            'isCourses' => true,
        ]);
    }

    #[Route('/new', name: 'app_forum_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function new(Request $request, ForumRepository $forumRepository): Response
    {
        throw $this->createNotFoundException();

        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumRepository->save($forum, true);

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/new.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_forum_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function show(Forum $forum): Response
    {
        throw $this->createNotFoundException();

        return $this->render('forum/show.html.twig', [
            'forum' => $forum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function edit(Request $request, Forum $forum, ForumRepository $forumRepository): Response
    {
        throw $this->createNotFoundException();

        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumRepository->save($forum, true);

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/edit.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, Forum $forum, ForumRepository $forumRepository): Response
    {
        throw $this->createNotFoundException();
        
        if ($this->isCsrfTokenValid('delete'.$forum->getId(), $request->request->get('_token'))) {
            $forumRepository->remove($forum, true);
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}
