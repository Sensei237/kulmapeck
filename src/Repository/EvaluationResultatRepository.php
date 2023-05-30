<?php

namespace App\Repository;

use App\Entity\EvaluationResultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvaluationResultat>
 *
 * @method EvaluationResultat|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvaluationResultat|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvaluationResultat[]    findAll()
 * @method EvaluationResultat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvaluationResultat::class);
    }

    public function save(EvaluationResultat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EvaluationResultat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EvaluationResultat[] Returns an array of EvaluationResultat objects
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

//    public function findOneBySomeField($value): ?EvaluationResultat
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
