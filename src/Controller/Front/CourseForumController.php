<?php

namespace App\Controller\Front;

use App\Entity\Cours;
use App\Entity\Forum;
use App\Entity\Membre;
use App\Entity\Sujet;
use App\Form\SujetType;
use App\Repository\ForumRepository;
use App\Repository\MembreRepository;
use App\Repository\SujetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/course-forum')]
class CourseForumController extends AbstractController
{
    #[Route('/{slug}', name: 'app_front_course_forum_index')]
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
            }
            else {
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


        return $this->render('front/course_forum/index.html.twig', [
            'controller_name' => 'CourseForumController',
            'course' => $cours,
            'heIsMembre' => $heIsMembre,
            'sujetForm' => $sujetForm,
            'membre' => $membre,
            'isForumPage' => true,
        ]);
    }

    #[Route('/{slug}/{reference}', name: 'app_front_course_forum_subject_message')]
    public function subjectMessage(Cours $cours, Sujet $sujet, MembreRepository $membreRepository)
    {
        $heIsMembre = false;
        $membre = $membreRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($membre !== null) {
           // if ($membre !== null && $membre->getForums()->contains($cours->getForum())) {
            $heIsMembre = true;
        }

        return $this->render('front/course_forum/subject_message.html.twig', [
            'controller_name' => 'CourseForumController',
            'course' => $cours,
            'sujet' => $sujet,
            'heIsMembre' => $heIsMembre,
            'membre' => $membre,
            'isForumPage' => true,
        ]);
    }
}
