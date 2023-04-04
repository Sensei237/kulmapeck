<?php

namespace App\Controller\Student;

use App\Repository\EleveRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NetworkController extends AbstractController
{
    #[Route('/student/network', name: 'app_student_network')]
    public function index(Request $request, EleveRepository $eleveRepository, PaginatorInterface $paginatorInterface): Response
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($eleve === null) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('student/network/index.html.twig', [
            'isNetWork' => true,
            'student' => $eleve,
            'network' => $paginatorInterface->paginate($eleve->getUtilisateur()->getPersonne()->getInvites(), $request->query->getInt('page', 1), 10),
            
        ]);
    }
}
