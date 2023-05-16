<?php

namespace App\Controller\Admin;

use App\Entity\ForumMessage;
use App\Entity\Sujet;
use App\Form\ForumMessageType;
use App\Repository\ForumMessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum/{reference}/messages')]
#[Security("is_granted('ROLE_ADMIN')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class ForumMessageController extends AbstractController
{
    #[Route('/', name: 'app_forum_message_index', methods: ['GET'])]
    public function index(Sujet $sujet): Response
    {
        return $this->render('admin/forum_message/index.html.twig', [
            'forum_messages' => $sujet->getForumMessages(),
            'sujet' => $sujet,
            'isForumController' => true,
            'isCourses' => true,
            'heIsMembre' => false,
            'membre' => null,
        ]);
    }

    #[Route('/new', name: 'app_forum_message_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FORUM_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function new(Request $request, ForumMessageRepository $forumMessageRepository): Response
    {
        throw $this->createNotFoundException();
        $forumMessage = new ForumMessage();
        $form = $this->createForm(ForumMessageType::class, $forumMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumMessageRepository->save($forumMessage, true);

            return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum_message/new.html.twig', [
            'forum_message' => $forumMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_message_show', methods: ['GET'])]
    public function show(ForumMessage $forumMessage): Response
    {
        throw $this->createNotFoundException();

        return $this->render('forum_message/show.html.twig', [
            'forum_message' => $forumMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_message_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FORUM_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function edit(Request $request, ForumMessage $forumMessage, ForumMessageRepository $forumMessageRepository): Response
    {
        throw $this->createNotFoundException();

        $form = $this->createForm(ForumMessageType::class, $forumMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumMessageRepository->save($forumMessage, true);

            return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum_message/edit.html.twig', [
            'forum_message' => $forumMessage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_forum_message_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function delete(Request $request, ForumMessage $forumMessage, ForumMessageRepository $forumMessageRepository): Response
    {
        throw $this->createNotFoundException();

        if ($this->isCsrfTokenValid('delete'.$forumMessage->getId(), $request->request->get('_token'))) {
            $forumMessageRepository->remove($forumMessage, true);
        }

        return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
