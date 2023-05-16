<?php

namespace App\Controller\Instructor;

use App\Entity\Exam;
use App\Form\ExamFormType;
use App\Repository\EnseignantRepository;
use App\Repository\ExamRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instructor/exam')]
class ExamController extends AbstractController
{
    #[Route('/', name: 'app_instructor_exam_index', methods: ['GET'])]
    public function index(Request $request, ExamRepository $examRepository, PaginatorInterface $paginatorInterface): Response
    {
        $exams = $examRepository->findBy(['user' => $this->getUser()], ['publishedAt' => 'DESC']);

        return $this->render('instructor/exam/index.html.twig', [
            'isExam' => true,
            'exams' => $paginatorInterface->paginate($exams, $request->query->getInt('page', 1), 5),
        ]);
    }

    #[Route('/new', name: 'app_instructor_exam_new', methods: ['POST', 'GET'])]
    #[Route('{reference}/edit', name: 'app_instructor_exam_edit', methods: ['POST', 'GET'])]
    public function edit(Exam $exam=null, Request $request, EnseignantRepository $enseignantRepository, ExamRepository $examRepository, FileUploader $fileUploader): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null || !$enseignant->isIsCertified() || ($exam !== null && $enseignant->getUtilisateur()->getId() !== $exam->getUser()->getId())) {
            throw $this->createAccessDeniedException();
        }
        
        if ($exam === null) {
            $exam = new Exam();
        }

        $examForm = $this->createForm(ExamFormType::class, $exam);
        $examForm->handleRequest($request);

        if ($examForm->isSubmitted() && $examForm->isValid()) {

            if ($exam->getReference() === null) {
                $exam->setReference(time() + $exam->getId());
            }
            $exam->setUser($this->getUser());
            $this->uploadImagesFiles($exam, $fileUploader);
            $examRepository->save($exam, true);

            return $this->redirectToRoute('app_instructor_exam_index');
        }

        return $this->render('instructor/exam/edit.html.twig', [
            'isExam' => true,
            'examForm' => $examForm->createView(),
            'exams' => $examRepository->findBy(['user' => $this->getUser()], ['publishedAt' => 'DESC'], 5),
            'exam' => $exam,

        ]);
    }

    #[Route('/{reference}/published', name: 'app_instructor_exam_published')]
    #[ParamConverter('exam', options: ['mapping' => ['reference' => 'reference']])]
    public function published(Exam $exam, ExamRepository $examRepository, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('published' . $exam->getId(), $request->query->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $isValid = false;

        if ($this->getUser() !== $exam->getUser()) {
            $this->addFlash('danger', "Vous ne pouvez pas effectuer cette opération !");
        }

        if ($isValid) {
            $exam->setIsPublished(true);
            $examRepository->save($exam, true);
        }

        return $this->redirectToRoute('app_instructor_exam_index');
    }

    #[Route('/{reference}/remove', name: 'app_instructor_exam_delete')]
    #[ParamConverter('exam', options: ['mapping' => ['reference' => 'reference']])]
    public function delete(Exam $exam, ExamRepository $examRepository, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('remove' . $exam->getId(), $request->query->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $isValid = true;

        if ($this->getUser() !== $exam->getUser()) {
            $isValid = false;
            $this->addFlash('danger', "Vous ne pouvez pas effectuer cette opération !");
        }

        if ($isValid) {
            $examRepository->remove($exam, true);
        }

        return $this->redirectToRoute('app_instructor_exam_index');
    }

    private function uploadImagesFiles(Exam $exam, FileUploader $fileUploader, $path = 'media/exams/files')
    {
        if ($exam->getSujetFile() != null) {

            $fileName = $fileUploader->upload($exam->getSujetFile(), $path);
            $exam->setSujet($fileName);
        }

        if ($exam->getCorrectionFile() != null) {

            $fileName = $fileUploader->upload($exam->getCorrectionFile(), $path);
            $exam->setCorrection($fileName);
        }

        if ($exam->image != null) {

            $fileName = $fileUploader->upload($exam->image, $path);
            $exam->setImageFile($fileName);
        }

    }

}
