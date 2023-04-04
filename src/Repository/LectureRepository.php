<?php

namespace App\Repository;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Lecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecture>
 *
 * @method Lecture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecture[]    findAll()
 * @method Lecture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LectureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecture::class);
    }

    public function save(Lecture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lecture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
        * @return Lecture Returns an  Lecture object
        */
    public function findStudentLastLecture(Eleve $eleve, Cours $cours): ?Lecture
    {
        return $this->createQueryBuilder('l')
            ->join('l.eleve', 'el')
            ->join('l.lesson', 'les')
            ->join('les.chapitre', 'chap')
            ->andWhere('l.eleve = :eleve')
            ->andWhere('chap.cours = :cours')
            ->setParameter('eleve', $eleve)
            ->setParameter('cours', $cours)
            ->orderBy('l.startAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Lecture Returns an  Lecture object
     */
    public function findStudentCourseLectures(Eleve $eleve, Cours $cours, bool $isFinished): ?array
    {
        return $this->createQueryBuilder('l')
            ->join('l.eleve', 'el')
            ->join('l.lesson', 'les')
            ->join('les.chapitre', 'chap')
            ->andWhere('l.eleve = :eleve')
            ->andWhere('chap.cours = :cours')
            ->andWhere('l.isFinished = :isFinished')
            ->setParameter('eleve', $eleve)
            ->setParameter('cours', $cours)
            ->setParameter('isFinished', $isFinished)
            ->orderBy('l.startAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Lecture
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
