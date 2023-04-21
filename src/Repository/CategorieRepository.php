<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function save(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Categorie[] Returns an array of Categorie objects
    */
    public function findBCategories(int $maxResult = null): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.cours', 'co')
            ->andWhere('co.isValidated = :isValidated')
            ->andWhere('c.isSubCategory = :isSubCategory')
            ->setParameter('isValidated', true)
            ->setParameter('isSubCategory', false)
            ->orderBy('c.name', 'ASC')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBCategoriesForum(int $maxResult = null): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.cours', 'co')
            ->join('co.forum', 'f')
            ->join('f.sujets', 's')
            ->andWhere('co.isValidated = :isValidated')
            ->setParameter('isValidated', true)
            ->orderBy('c.name', 'ASC')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCats(bool $isSub = false) 
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isSubCategory = :isSub')
            ->setParameter('isSub', $isSub)
            ->orderBy('c.name', 'ASC');
    }

//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
