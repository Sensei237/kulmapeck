<?php

namespace App\Controller\Student;

use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/student/money')]
class MoneyBankController extends AbstractController
{
    private $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

   /**
 * @Groups({"eleve", "retrait"})
 */
#[Route('/', methods: ['GET'])]
public function getStudentMoney(
    EleveRepository $eleveRepository,
    NetworkConfigRepository $networkConfigRepository
): JsonResponse {
    $student = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

    if (!$student) {
        throw $this->createAccessDeniedException();
    }

    // Explicitly join the related Retrait entities
    $studentWithRetraits = $eleveRepository->createQueryBuilder('e')
        ->leftJoin('e.utilisateur', 'u')
        ->leftJoin('u.retraits', 'r')
        ->where('e.id = :studentId')
        ->setParameter('studentId', $student->getId())
        ->getQuery()
        ->getOneOrNullResult();

    if (!$studentWithRetraits) {
        throw $this->createNotFoundException('Student not found with related retraits.');
    }

    $networkConfig = $networkConfigRepository->findOneBy([]);
    $retraits = $studentWithRetraits->getUtilisateur()->getRetraits();

    $payoutList = $this->serializer->serialize($retraits, 'json', ['groups' => ['retraits']]);

    return $this->json([
        'student' => $this->serializer->serialize($student, 'json', ['groups' => ['user']]),
        'payoutAmount' => $this->calculatePayoutAmount($retraits),
        'payout' => json_decode($payoutList, true),
        'config' => $networkConfig,
    ], 200);
}


    private function calculatePayoutAmount($retraits): float
    {
        $montantTotal = 0;

        foreach ($retraits as $retrait) {
            $montantTotal += $retrait->getMontant();
        }

        return $montantTotal;
    }
}
