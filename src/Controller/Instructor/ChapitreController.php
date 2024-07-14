<?php

namespace App\Controller\Instructor;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Form\ChapitreType;
use App\Repository\ChapitreRepository;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/instructor/{slug}/chapitre')]
class ChapitreController extends AbstractController
{
    #[Route('/', name: 'app_instructor_chapitre_index', methods: ['GET'])]
    public function index(Cours $cour, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $cour->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('instructor/chapitre/index.html.twig', [
            'chapitres' => $cour->getChapitres(),
            'cours' => $cour,
        ]);
    }

    #[Route('/new', name: 'app_instructor_chapitre_new', methods: ['GET', 'POST'])]
    public function new(Cours $cour, Request $request, EnseignantRepository $enseignantRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $cour->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($cour->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        $chapitre = new Chapitre();
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chapitre->setCours($cour);
            $chapitre->setSlug($slugger->slug($chapitre->getTitle().' '.time().' '.$chapitre->getId()));
            $entityManager->persist($chapitre);
            $entityManager->flush();

            return $this->redirectToRoute('app_instructor_chapitre_index', ['slug' => $cour->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructor/chapitre/new.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form,
            'cours' => $cour,
        ]);
    }

    // #[Route('/{slugChap}', name: 'app_instructor_chapitre_show', methods: ['GET'])]
    // #[ParamConverter('cour', options: ['mapping' => ['slug' => 'slug']])]
    // #[ParamConverter('chapitre', options: ['mapping' => ['slugChap' => 'slug']])]
    // public function show(Cours $cour, Chapitre $chapitre, EnseignantRepository $enseignantRepository): Response
    // {
    //     $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
    //     if ($enseignant === null || !($enseignant === $cour->getEnseignant())) {
    //         throw $this->createAccessDeniedException();
    //     }

    //     return $this->render('instructor/chapitre/show.html.twig', [
    //         'chapitre' => $chapitre,
    //     ]);
    // }

    #[Route('/{slugChap}/edit', name: 'app_instructor_chapitre_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('cour', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('chapitre', options: ['mapping' => ['slugChap' => 'slug']])]
    public function edit(Cours $cour, Request $request, Chapitre $chapitre, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $cour->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($cour->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_instructor_chapitre_index', ['slug' => $cour->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructor/chapitre/edit.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form,
            'cours' => $cour,
        ]);
    }

    #[Route('/{slugChap}', name: 'app_instructor_chapitre_delete', methods: ['POST'])]
    #[ParamConverter('cour', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('chapitre', options: ['mapping' => ['slugChap' => 'slug']])]
    public function delete(Cours $cour, Request $request, Chapitre $chapitre, EnseignantRepository $enseignantRepository, EntityManagerInterface $entityManager): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !($enseignant === $cour->getEnseignant())) {
            throw $this->createAccessDeniedException();
        }

        if ($cour->isIsPublished()) {
            throw $this->createAccessDeniedException("Opération impossible ! Le cours est déjà publié");
        }

        if ($this->isCsrfTokenValid('delete'.$chapitre->getId(), $request->request->get('_token'))) {
            foreach ($chapitre->getLessons() as $lesson) {
                $entityManager->remove($lesson);
            }
            $entityManager->remove($chapitre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_instructor_chapitre_index', ['slug' => $cour->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
