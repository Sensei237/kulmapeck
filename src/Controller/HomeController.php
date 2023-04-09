<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Lesson;
use App\Entity\Media;
use App\Entity\PaymentMethod;
use App\Repository\CategorieRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\PaymentMethodRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EnseignantRepository $enseignantRepository, EleveRepository $eleveRepository): Response
    {
        if ($enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]) !== null) {
            return $this->redirectToRoute('app_instructor_home');
        }
        elseif ($eleveRepository->findOneBy(['utilisateur' => $this->getUser()]) !== null) {
            return $this->redirectToRoute('app_student_home');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/add-defaul-courses', name: 'app_add_def_courses')]
    public function courses(EnseignantRepository $er, PaymentMethodRepository $paymentMethodRepository, FileUploader $fileUploader, CategorieRepository $categorieRepository, EntityManagerInterface $em, CoursRepository $cr, SluggerInterface $sluggerInterface)
    {
        $faker = Factory::create('fr_FR');
        $enseignant = $er->findOneBy([]);
        $categories = $categorieRepository->findAll();
        $paymentsMethods = $paymentMethodRepository->findAll();

        for ($i = 0; $i < 200; $i++) {
            $cours = new Cours();
            $intitule = $faker->sentence(6);
            $media = new Media();
            $media->setImageFile("01-641f0ad1daef2.jpg");
            $numberOfLessons = 12;
            $cours->setIntitule($intitule)
                ->setEnseignant($enseignant)
                ->setSlug($sluggerInterface->slug($intitule . ' ' . $i+time()))
                ->setContent($faker->paragraphs($faker->numberBetween(3, 8), true))
                ->setDescription($faker->paragraph(5))
                ->setIsFree($i % 4 != 0)
                ->setNiveauDifficulte('Facile')
                ->setIsPublished($i % 10 != 0)
                ->setIsValidated($i % 10 != 0)
                ->setDureeApprentissage($faker->numberBetween(4, 24) . ' heures')
                ->setLanguage($i % 2 == 0 ? 'English' : 'French')
                ->setNumberOfLessons($numberOfLessons)
                ->setVues(0)
                ->setMedia($media);
                if (!$cours->isIsFree()) {
                    $cours->setMontantAbonnement($faker->numberBetween(1500, 20000));
                    foreach ($paymentsMethods as $pm) {
                        $cours->addPaymentMethod($pm);
                    }
                }
                if (count($categories) > 0) {
                    $cours->setCategorie($categories[$faker->numberBetween(0, count($categories)-1)]);
                }
            $numero = 1;
            $numeroLesson = 1;
            for ($j = 0; $j < 4; $j++) {
                $chap = new Chapitre();
                $title = $faker->sentence(5);
                $chap->setCours($cours)
                    ->setTitle($title)
                    ->setSlug($sluggerInterface->slug($title . ' ' . time()))
                    ->setDescription($faker->paragraph(3))
                    ->setNumero($numero);
                $em->persist($chap);
                $numero++;
                for ($k = 0; $k < 3; $k++) {
                    $lesson = new Lesson();
                    $title = $faker->sentence(6);
                    $lesson->setChapitre($chap)
                        ->setTitle($title)
                        ->setSlug($sluggerInterface->slug($title . ' ' . time()))
                        ->setContent($faker->paragraphs($faker->numberBetween(5, 12), true))
                        ->setNumero($numeroLesson);
                    
                    $numeroLesson++;
                    $em->persist($lesson);
                    $chap->addLesson($lesson);
                }
                $em->persist($chap);
            }

            $media->setCours($cours);
            $cr->save($cours);
        }

        $em->flush();

        return $this->redirectToRoute('app_front');
    }
}
