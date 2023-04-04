<?php

namespace App\Controller\Instructor;

use App\Repository\EnseignantRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NetworkController extends AbstractController
{
    #[Route('/instructor/network', name: 'app_instructor_network')]
    public function index(Request $request, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface): Response
    {
        
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('instructor/network/index.html.twig', [
            'isNetwork' => true,
            'enseignant' => $enseignant,
            'network' => $paginatorInterface->paginate($enseignant->getUtilisateur()->getPersonne()->getInvites(), $request->query->getInt('page', 1), 10),

        ]);
    }
}
