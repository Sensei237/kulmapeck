<?php

namespace App\Repository;

use App\Entity\Eleve;
use App\Entity\Enseignant;
use App\Entity\Payment;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
        * @return Payment[] Returns an array of Payment objects
        */
    public function findTeacherCoursesPayments(Enseignant $enseignant, string $sort= 'p.paidAt', string $order='DESC'): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.cours', 'co')
            ->andWhere('co.enseignant = :enseignant')
            ->setParameter('enseignant', $enseignant)
            ->orderBy($sort, $order)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findStudentActivePlan(Eleve $eleve): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.eleve = :eleve')
            ->andWhere('p.cours = :empty')
            ->andWhere('p.isExpired = :isExpired')
            ->setParameter('eleve', $eleve)
            ->setParameter('empty', null)
            ->setParameter('isExpired', false)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
    * @return Payment[] Returns an array of Payment objects
    */
    public function findBetweenDates(DateTime $dateStart, DateTime $dateEnd): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.paidAt >= :dateStart')
            ->andWhere('p.paidAt <= :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Payment
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
