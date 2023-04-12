<?php

namespace App\Controller\Instructor;

use App\Entity\Cours;
use App\Entity\Forum;
use App\Entity\ForumMessage;
use App\Entity\Membre;
use App\Entity\Sujet;
use App\Form\SujetType;
use App\Repository\ForumMessageRepository;
use App\Repository\ForumRepository;
use App\Repository\MembreRepository;
use App\Repository\SujetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('instructor/forum')]
class CourseForumController extends AbstractController
{
    #[Route('/{slug}', name: 'app_instructor_course_forum_index')]
    public function index(Cours $cours, MembreRepository $membreRepository, SujetRepository $sujetRepository, ForumRepository $forumRepository): Response
    {
        if (!$cours->getForum()) {
            if ($cours->isIsValidated()) {
                $forum = new Forum();
                $forum->setCours($cours);
                $cours->setForum($forum);
                $membre = $membreRepository->findOneBy(['utilisateur' => $cours->getEnseignant()->getUtilisateur()]);
                if (!$membre) {
                    $membre = new Membre();
                    $membre->setUtilisateur($cours->getEnseignant()->getUtilisateur());
                    $forum->addMembre($membre);
                }
                $membre->addForum($forum);
                $forumRepository->save($forum, true);
            } else {
                throw $this->createNotFoundException();
            }
        }

        $heIsMembre = false;
        $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($membre !== null && $membre->getForums()->contains($cours->getForum())) {
            $heIsMembre = true;
        }

        $sujet = new Sujet();
        $sujetForm = $this->createForm(SujetType::class, $sujet, [
            'action' => $heIsMembre ? $this->generateUrl('app_front_course_new_forum', ['id' => $membre->getId(), 'slug' => $cours->getSlug()])  : '',
        ]);


        return $this->render('instructor/course_forum/index.html.twig', [
            'controller_name' => 'CourseForumController',
            'course' => $cours,
            'heIsMembre' => $heIsMembre,
            'sujetForm' => $sujetForm,
            'membre' => $membre,
            'isForumPage' => true,
            'heIsInstructor' => true,
        ]);
    }

    #[Route('/{slug}/{reference}/subject-messages', name: 'app_instructor_course_forum_subject_message')]
    public function subjectMessage(Cours $cours, Sujet $sujet, MembreRepository $membreRepository)
    {
        $heIsMembre = false;
        $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($membre !== null && $membre->getForums()->contains($cours->getForum())) {
            $heIsMembre = true;
        }

        return $this->render('instructor/course_forum/subject_message.html.twig', [
            'controller_name' => 'CourseForumController',
            'course' => $cours,
            'sujet' => $sujet,
            'heIsMembre' => $heIsMembre,
            'membre' => $membre,
            'isForumPage' => true,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_instructor_course_forum_subject_delete', methods: ['POST'])]
    public function deleteSubjet(Sujet $sujet, SujetRepository $sujetRepository, ForumMessageRepository $forumMessageRepository, Request $request): Response 
    {
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->request->get('_token'))) {
            $sujetRepository->remove($sujet, true);
        }

        return $this->redirectToRoute('app_instructor_course_forum_index', ['slug' => $sujet->getForum()->getCours()->getSlug()]);
    }

    #[Route('/{id}/delete-message', name: 'app_instructor_course_forum_subject_delete_message', methods: ['GET'])]
    public function removeForumMessage(ForumMessage $forumMessage, ForumMessageRepository $forumMessageRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $forumMessage->getId(), $request->query->get('_token'))) {
            $forumMessageRepository->remove($forumMessage, true);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['hasDone' => true]);
        }

        return $this->redirectToRoute('app_instructor_course_forum_subject_message', ['slug' => $forumMessage->getSujet()->getForum()->getCours()->getSlug(), 'reference' => $forumMessage->getSujet()->getReference()]);
    }
    
}
