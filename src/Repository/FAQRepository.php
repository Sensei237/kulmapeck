<?php

namespace App\Repository;

use App\Entity\FAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FAQ>
 *
 * @method FAQ|null find($id, $lockMode = null, $lockVersion = null)
 * @method FAQ|null findOneBy(array $criteria, array $orderBy = null)
 * @method FAQ[]    findAll()
 * @method FAQ[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FAQRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAQ::class);
    }

    public function save(FAQ $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FAQ $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findFAQs(bool $isValidated = true): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.cours', 'c')
            ->andWhere('c.isValidated = :isValidated')
            ->setParameter('isValidated', $isValidated)
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return FAQ[] Returns an array of FAQ objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FAQ
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
