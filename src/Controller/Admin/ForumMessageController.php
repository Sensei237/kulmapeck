<?php

namespace App\Controller\Admin;

use App\Entity\ForumMessage;
use App\Form\ForumMessageType;
use App\Repository\ForumMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum/message')]
class ForumMessageController extends AbstractController
{
    #[Route('/', name: 'app_forum_message_index', methods: ['GET'])]
    public function index(ForumMessageRepository $forumMessageRepository): Response
    {
        return $this->render('forum_message/index.html.twig', [
            'forum_messages' => $forumMessageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_forum_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ForumMessageRepository $forumMessageRepository): Response
    {
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
        return $this->render('forum_message/show.html.twig', [
            'forum_message' => $forumMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumMessage $forumMessage, ForumMessageRepository $forumMessageRepository): Response
    {
        $form = $this->createForm(ForumMessageType::class, $forumMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumMessageRepository->save($forumMessage, true);

            return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum_message/edit.html.twig', [
            'forum_message' => $forumMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_message_delete', methods: ['POST'])]
    public function delete(Request $request, ForumMessage $forumMessage, ForumMessageRepository $forumMessageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forumMessage->getId(), $request->request->get('_token'))) {
            $forumMessageRepository->remove($forumMessage, true);
        }

        return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
