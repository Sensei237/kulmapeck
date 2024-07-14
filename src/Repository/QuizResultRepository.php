<?php

namespace App\Repository;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\QuizResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizResult>
 *
 * @method QuizResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizResult[]    findAll()
 * @method QuizResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResult::class);
    }

    public function save(QuizResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QuizResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
        * @return QuizResult[] Returns an array of QuizResult objects
        */
   public function findStudentQuizResultsByCourseOrChapter(Eleve $eleve, ?Cours $cours, ?Chapitre $chapitre): array
{
    $query = $this->createQueryBuilder('q')
        ->join('q.quiz', 'qz');
    
    if ($chapitre !== null) {
        $query->join('qz.chapitre', 'chap')
            ->andWhere('qz.chapitre = :chapitre')
            ->setParameter('chapitre', $chapitre);
    } elseif ($cours !== null) {
        $query->join('qz.cours', 'co')
            ->andWhere('qz.cours = :cours')
            ->setParameter('cours', $cours);
    }

    $query->andWhere('q.eleve = :eleve')
        ->setParameter('eleve', $eleve);

    $results = $query->getQuery()
        ->getResult();

    // Use a set to ensure uniqueness based on quiz entities
    $uniqueQuizzes = [];
    foreach ($results as $result) {
        $quiz = $result->getQuiz();
        $uniqueQuizzes[$quiz->getId()] = $result;
    }

    return array_values($uniqueQuizzes);
}


//    public function findOneBySomeField($value): ?QuizResult
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
