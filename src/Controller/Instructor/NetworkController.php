<?php

namespace App\Controller\Instructor;

use App\Entity\Retrait;
use App\Form\RetraitType;
use App\Repository\EnseignantRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\RetraitRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NetworkController extends AbstractController
{
    public function __construct(private Keys $keys)
    {
        
    }
    
    #[Route('/instructor/network', name: 'app_instructor_network')]
    public function index(Request $request, NetworkConfigRepository $networkConfigRepository, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface): Response
    {
        
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        $networkConfig = $networkConfigRepository->findOneBy([]);

        return $this->render('instructor/network/index.html.twig', [
            'isNetwork' => true,
            'enseignant' => $enseignant,
            'network' => $paginatorInterface->paginate($enseignant->getUtilisateur()->getPersonne()->getInvites(), $request->query->getInt('page', 1), 10),
            'networkConfig' => $networkConfig,

        ]);
    }

    #[Route('/instructor/retrait/new', name: 'app_instructor_network_retrait', methods: ['POST', 'GET'])]
    public function retirer(Request $request, EnseignantRepository $enseignantRepository, RetraitRepository $retraitRepository, NetworkConfigRepository $networkConfigRepository, UserRepository $userRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        $retrait = new Retrait();
        $form = $this->createForm(RetraitType::class, $retrait);
        $form->handleRequest($request);

        $networkConfig = $networkConfigRepository->findOneBy([]);
        if ($networkConfig === null) {
            throw $this->createAccessDeniedException("Impossible d'effectuer le retrait pour le moment");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $retrait->setUser($enseignant->getUtilisateur());
            $retraitRepository->save($retrait);

            $msg = ManageNetwork::convertInMoney($enseignant->getUtilisateur(), $retrait->getMontant(), $retrait->getNumeroTelephone(), $networkConfig, $userRepository, $this->keys);
            
            $this->addFlash('success', $msg['message']);
            
            return $this->redirectToRoute('app_instructor_network_retrait');
        }

        return $this->render('instructor/network/retrait.html.twig', [
            'isNetwork' => true,
            'enseignant' => $enseignant,
            'form' => $form->createView(),
            'networkConfig' => $networkConfig,
        ]);
    }

    #[Route('/instructor/retraits', name: 'app_instructor_network_retraits', methods: ['GET'])]
    public function retraits(EnseignantRepository $enseignantRepository, NetworkConfigRepository $networkConfigRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        $networkConfig = $networkConfigRepository->findOneBy([]);
        
        $retraits = $enseignant->getUtilisateur()->getRetraits();
        $montantTotal = 0;
        foreach ($retraits as $retrait) {
            $montantTotal += $retrait->getMontant();
        }

        return $this->render('instructor/network/retrait.html.twig', [
            'isNetwork' => true,
            'enseignant' => $enseignant,
            'networkConfig' => $networkConfig,
            'retraits' => $retraits,
            'showHistorique' => true,
            'montantTotal' => $montantTotal,
        ]);
    }

}
