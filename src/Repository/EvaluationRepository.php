<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Evaluation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 *
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function save(Evaluation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evaluation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updatePassedDate(\DateTime $currentDate): void
    {
        $data = $this->createQueryBuilder('e')
                ->andWhere('e.endAt < :currentDate')
                ->setParameter('currentDate', $currentDate)
                ->getQuery()
                ->getResult();
        foreach ($data as $d) {
            $d->setIsPassed(true);
            $this->save($d);
        }
        $this->getEntityManager()->flush();
    }

    /**
    * @return Evaluation[] Returns an array of Evaluation objects
    */
    public function findSudentEvaluationsAnnonces(Classe $classe): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.classes', 'cl')
            ->andWhere('cl.id = :idClasse')
            ->setParameter('idClasse', $classe->getId())
            ->andWhere('e.isPassed = :isPassed')
            ->setParameter('isPassed', false)
            ->orderBy('e.endAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Evaluation[] Returns an array of Evaluation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evaluation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}