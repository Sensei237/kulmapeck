<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Repository\ExamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_COURSE_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class ExamController extends AbstractController
{
    #[Route('/admin/exam', name: 'app_admin_exam')]
    public function index(Request $request, PaginatorInterface $paginatorInterface, ExamRepository $examRepository): Response
    {
        $exams = $examRepository->findBy([], ['publishedAt' => 'DESC']);

        return $this->render('admin/exam/index.html.twig', [
            'controller_name' => 'ExamController',
            'exc' => true,
            'isCourses' => true,
            'exams' => $paginatorInterface->paginate($exams, $request->query->getInt('page', 1), 10),

        ]);
    }

    #[Route('/admin/exam/{reference}/remove', name: 'app_admin_exam_remove')]
    public function remove(Exam $exam, ExamRepository $examRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $exam->getId(), $request->query->get('_token'))) {
            $examRepository->remove($exam, true);
        }

        return $this->redirectToRoute('app_admin_exam', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/exam/{reference}/approve', name: 'app_admin_exam_approve')]
    public function approved(Exam $exam, ExamRepository $examRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('validated' . $exam->getId(), $request->query->get('_token'))) {
            // dd("");
            $exam->setIsPublished(true)->setIsValidated(true);
            $examRepository->save($exam, true);
        }

        return $this->redirectToRoute('app_admin_exam', [], Response::HTTP_SEE_OTHER);
    }
}
