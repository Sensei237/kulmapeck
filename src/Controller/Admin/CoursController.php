<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use App\Entity\Forum;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_admin_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($request->get('search') !== null) {
            $courses = $coursRepository->searchByAdmin($request->query->get('search'));
        } else {
            switch ($request->query->get('filter')) {
                case 'free':
                    $courses = $coursRepository->findBy(['isFree' => true], ['isPublished' => 'ASC', 'createdAt' => 'DESC']);
                    break;
                case 'newest':
                    $courses = $coursRepository->findBy(['isPublished' => true,], ['createdAt' => 'DESC']);
                    break;
                case 'premium':
                    $courses = $coursRepository->findBy(['isFree' => false], ['isPublished' => 'ASC', 'createdAt' => 'DESC']);
                    break;
                case 'oldest':
                    $courses = $coursRepository->findBy(['isPublished' => true], ['created' => 'ASC']);
                    break;
                case 'accepted':
                    $courses = $coursRepository->findBy(['isValidated' => true], ['createdAt' => 'DESC']);
                    break;
                case 'rejected':
                    $courses = $coursRepository->findBy(['isRejected' => true], ['createdAt' => 'DESC']);
                default:
                    $courses = $coursRepository->findAll();
                    break;
            }
        }

        return $this->render('admin/cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
            'activatedCourses' => $coursRepository->findBy(['isValidated' => true]),
            'pendingCourses' => $coursRepository->findBy(['isValidated' => false, 'isPublished' => true]),
            'courses' => $paginatorInterface->paginate($courses, $request->query->getInt('page', 1), 10),
            'coc' => true,
            'isCourses' => true,
            'filter' => $request->query->get('filter'),
            'search' => $request->query->get('search')
        ]);
    }

    #[Route('/new', name: 'app_admin_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoursRepository $coursRepository, SluggerInterface $slugger): Response
    {
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cour->setSlug($slugger->slug(time() . '-' . $cour->getIntitule()));
            $coursRepository->save($cour, true);

            return $this->redirectToRoute('app_admin_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
            'coc' => true,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_cours_show', methods: ['GET'])]
    public function show(Cours $course, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        return $this->render('admin/cours/show.html.twig', [
            'course' => $course,
            'coc' => true,
            'eleves' => $paginatorInterface->paginate($course->getEleves(), $request->query->getInt('page', 1), 5),
            'reviews' => $paginatorInterface->paginate($course->getReviews(), $request->query->getInt('page', 1), 5),
            'isCourses' => true,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_cours_approve', methods: ['GET'])]
    public function approveCourse(Cours $course, CoursRepository $coursRepository)
    {
        $forum = $course->getForum();
        if ($forum === null) {
            $forum = new Forum();
            $course->setForum($forum);
        }
        $course->setIsValidated(true);
        $coursRepository->save($course, true);
    }

    #[Route('/{slug}', name: 'app_admin_cours_reject', methods: ['GET'])]
    public function rejectCourse(Cours $course, CoursRepository $coursRepository)
    {
        $course->setIsRejected(true);
        $coursRepository->save($course, true);
    }

    #[Route('/{id}/edit', name: 'app_admin_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, CoursRepository $coursRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cour->setSlug($slugger->slug(time() . '-' . $cour->getIntitule()));
            $coursRepository->save($cour, true);

            return $this->redirectToRoute('app_admin_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
            'coc' => true,
            'isCourses' => true
        ]);
    }

    #[Route('/{id}', name: 'app_admin_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, CoursRepository $coursRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $coursRepository->remove($cour, true);
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
