<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/categorie')]
#[Security("is_granted('ROLE_COURSE_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_admin_categorie_index', methods: ['GET', 'POST'])]
    public function index(CategorieRepository $categorieRepository, SluggerInterface $slugger, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($slugger->slug($categorie->getName()));
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $paginatorInterface->paginate($categorieRepository->findAll(), $request->query->getInt('page', 1), 15),
            'cac' => true,
            'isCourses' => true,
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieRepository $categorieRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($slugger->slug($categorie->getName()));
            $this->uploadCourseMedia($categorie, $fileUploader);
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
            'cac' => true
        ]);
    }

    #[Route('/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('admin/categorie/show.html.twig', [
            'categorie' => $categorie,
            'cac' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie, [
            'action' => $this->generateUrl('app_admin_categorie_edit', ['id' => $categorie->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($slugger->slug($categorie->getName()));
            $this->uploadCourseMedia($categorie, $fileUploader);
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
            'cac' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadCourseMedia(Categorie $categorie, FileUploader $fileUploader)
    {
        if ($categorie->image !== null) {
            $fileName = $fileUploader->upload($categorie->image, 'media/courses/categories');
            $categorie->setImageFile($fileName);
        }
    }
}
