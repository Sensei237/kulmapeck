<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Enseignant;
use App\Entity\Filiere;
use App\Entity\Specialite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Cours[] Returns an array of Cours objects
    */
   public function search(string $value): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('c.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function searchByInstructor(Enseignant $enseignant, string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.enseignant = :enseignant')
            ->andWhere('c.intitule LIKE :val')
            ->setParameter('enseignant', $enseignant)
            ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function searchByAdmin(string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.intitule LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function findByCategory(Categorie $categorie): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.categorie', 'cat')
            // ->join('cat.category', 'subCat')
            ->andWhere('c.categorie = :category')
            ->andWhere('c.isValidated = :isValidated')
            ->setParameter('category', $categorie)
            ->setParameter('isValidated', true)
            ->getQuery()
            ->getResult();
    }

    public function findCoursesSubject(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.forum', 'f')
            ->join('f.subject', 's')
            ->andWhere('c.isActivated = :isActivated')
            ->setParameter('isActivated', true)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function findForStudent(Eleve $eleve, ?string $search = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.eleves', 'e')
            // ->join('cat.category', 'subCat')
            ->andWhere('e.id = :id')
            ->setParameter('id', $eleve->getId());

            if ($search !== null) {
                $query->andWhere('c.intitule LIKE :val')->setParameter('val', '%' . $search . '%');
            }
            
        return $query->orderBy('c.intitule', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function frontSearch(?Categorie $categorie, ?string $text, ?bool $isFree, ?string $level, ?string $language, ?Filiere $filiere, ?Specialite $specialite, ?Classe $classe): array
    {
        $query = $this->createQueryBuilder('c');
        if ($categorie) {
            $query->join('c.categorie', 'cat');
        }
        if ($classe) {
            $query->join('c.classe', 'cl');
        }
        if ($specialite) {
            $query->join('c.classe', 'cls');
            $query->join('cls.specialite', 's');
        }
        if ($filiere) {
            $query->join('c.classe', 'scls');
            $query->join('scls.specialite', 'sps');
            $query->join('sps.filiere', 'f');
        }

        if ($categorie !== null) {
            $query->andWhere('c.categorie = :category')->setParameter('category', $categorie);
        }
        if ($text !== null) {
            $query->andWhere('c.intitule LIKE :val')->setParameter('val', '%' . $text . '%');
        }
        if ($isFree !== null) {
            $query->andWhere('c.isFree = :isFree')->setParameter('isFree', $isFree);
        }
        if ($level !== null) {
            $query->andWhere('c.niveauDifficulte = :level')->setParameter('level', $level);
        }
        if ($level !== null) {
            $query->andWhere('c.language = :language')->setParameter('language', $language);
        }
        if ($filiere !== null) {
            $query->andWhere('sps.filiere = :filiere')->setParameter('filiere', $filiere);
        }
        if ($specialite !== null) {
            $query->andWhere('cls.specialite = :specialite')->setParameter('specialite', $specialite);
        }
        if ($classe !== null) {
            $query->andWhere('cl.id = :classe')->setParameter('classe', $classe->getId());
        }
  
        $query->andWhere('c.isValidated = :isValidated')->setParameter('isValidated', true);

        return $query->getQuery()
            ->getResult();
    }

    public function findForChefDepartement(User $user): array
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.classe', 'cl')
            ->join("cl.specialite", 'sp');

        foreach ($user->getFilieres() as $key => $filiere) {
            $query->orWhere('sp.filiere = :filiere' . $key)
                ->setParameter('filiere' . $key, $filiere);
        }
        
        return $query->getQuery()
            ->getResult();
    }
}
