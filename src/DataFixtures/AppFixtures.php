<?php

namespace App\DataFixtures;

use App\Entity\Enseignant;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $enseignantRepo;
    private $userRepo;
    private $eleveRepo;
    private $classeRepo;
    private $coursRepo;

    public function __construct(UserRepository $userRepo, EnseignantRepository $enseignantRepo, EleveRepository $eleveRepo, ClasseRepository $classeRepo, CoursRepository $coursRepo)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $this->addClasses();

        $this->addAdmins();

        $this->addCourses();

        $manager->flush();
    }

    private function addClasses(): void 
    {
        $faker = Factory::create('fr_FR');
        
        $sousSystemes = ['Sous-système Anglophone', 'Sous-système Francophone'];
        $typesEnseignements = [
            'Enseignement Général' => [
                'Serie C' => [],
                'Serie D' => [],
                'Serie A' => [
                    'Série A4 Allemande',
                    'Série A4 Espagnole'
                ], 
            ], 
            'Enseignement Technique' => [
                'Série E' => [],
                'Série G' => [
                    'STT',
                    'ESF'
                ],
                'Génie Civil' => [
                    'Batiments et travaux public',
                    'Installation Sanitaire'
                ],
                'Génie Electrique' => [
                    'Electro technique',
                    'Electronique'
                ],
                'Menuiserie' => [
                    'Menuiserie',
                    'MEB'
                ], 
            ]
        ];

        for ($i=0; $i <= 25; $i++) {

        }
    }

    private function addAdmins():void 
    {

    }

    private function addTrainers():void 
    {

    }
    
    private function addCourses(): void 
    {

    }

    private function addStudents(): void
    {

    }

    
}
