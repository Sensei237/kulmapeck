<?php

namespace App\Controller\Instructor;

use App\Entity\Chapitre;
use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/instructor/{slug}/lesson')]
class LessonController extends AbstractController
{
    #[Route('/', name: 'app_instructor_lesson_index', methods: ['GET'])]
    public function index(Chapitre $chapitre, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $chapitre->getCours()->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('instructor/lesson/index.html.twig', [
            'lessons' => $chapitre->getLessons(),
            'chapitre' => $chapitre,
        ]);
    }

    #[Route('/new', name: 'app_instructor_lesson_new', methods: ['GET', 'POST'])]
    public function new(Chapitre $chapitre, Request $request, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $chapitre->getCours()->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($chapitre->getCours()->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lesson->setSlug($slugger->slug($lesson->getTitle().' '.time().' '. $lesson->getId()))
                ->setChapitre($chapitre);
                
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('app_instructor_lesson_index', ['slug' => $chapitre->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructor/lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'chapitre' => $chapitre,
        ]);
    }

    // #[Route('/{id}', name: 'app_instructor_lesson_show', methods: ['GET'])]
    // public function show(Lesson $lesson): Response
    // {
    //     return $this->render('instructor/lesson/show.html.twig', [
    //         'lesson' => $lesson,
    //     ]);
    // }

    #[Route('/{slugLesson}/edit', name: 'app_instructor_lesson_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('lesson', options: ['mapping' => ['slugLesson' => 'slug']])]
    #[ParamConverter('chapitre', options: ['mapping' => ['slug' => 'slug']])]
    public function edit(Chapitre $chapitre, Request $request, Lesson $lesson, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $chapitre->getCours()->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($chapitre->getCours()->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lesson->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_instructor_lesson_index', ['slug' => $chapitre->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructor/lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'chapitre' => $chapitre,
        ]);
    }

    #[Route('/{slugLesson}', name: 'app_instructor_lesson_delete', methods: ['POST'])]
    #[ParamConverter('lesson', options: ['mapping' => ['slugLesson' => 'slug']])]
    #[ParamConverter('chapitre', options: ['mapping' => ['slug' => 'slug']])]
    public function delete(Chapitre $chapitre, Request $request, Lesson $lesson, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $chapitre->getCours()->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($chapitre->getCours()->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_instructor_lesson_index', ['slug' => $chapitre->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
