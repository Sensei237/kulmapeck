<?php

namespace App\Repository;

use App\Entity\LikeMessageForum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LikeMessageForum>
 *
 * @method LikeMessageForum|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeMessageForum|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeMessageForum[]    findAll()
 * @method LikeMessageForum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeMessageForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeMessageForum::class);
    }

    public function save(LikeMessageForum $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LikeMessageForum $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LikeMessageForum[] Returns an array of LikeMessageForum objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LikeMessageForum
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
