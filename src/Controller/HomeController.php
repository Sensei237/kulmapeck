<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Lesson;
use App\Entity\Media;
use App\Entity\Quiz;
use App\Repository\CategorieRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\QuizRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function courses(EnseignantRepository $er, QuizRepository $quizRepository, PaymentMethodRepository $paymentMethodRepository, FileUploader $fileUploader, CategorieRepository $categorieRepository, EntityManagerInterface $em, CoursRepository $cr, SluggerInterface $sluggerInterface)
    {
        $faker = Factory::create('fr_FR');
        $enseignant = $er->findOneBy([]);
        $categories = $categorieRepository->findAll();
        $paymentsMethods = $paymentMethodRepository->findAll();

        for ($i = 0; $i < 20; $i++) {
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
                for ($q = 0; $q < $faker->numberBetween(8, 12); $q++) {
                    $quiz = new Quiz();
                    $quiz->setQuestion($faker->paragraph());
                    $quiz->setProposition1($faker->sentence($faker->numberBetween(6, 15)))
                        ->setProposition2($faker->sentence($faker->numberBetween(6, 15)))
                        ->setProposition3($faker->sentence($faker->numberBetween(6, 15)))
                        ->setReference(time()+$faker->numberBetween(1000)+$chap->getId())
                        ->setProposition4($faker->sentence($faker->numberBetween(6, 15)));
                    $quiz->setPropositionJuste([$faker->numberBetween(1, 4)]);
                    $quiz->setChapitre($chap);
                    $quiz->setCours($cours);
                    $em->persist($quiz);
                }
            }

            $media->setCours($cours);
            $cr->save($cours);
            for ($q = 0; $q < $faker->numberBetween(10, 12); $q++) {
                $quiz = new Quiz();
                $quiz->setQuestion($faker->paragraph());
                $quiz->setProposition1($faker->sentence($faker->numberBetween(6, 15)))
                    ->setProposition2($faker->sentence($faker->numberBetween(6, 15)))
                    ->setProposition3($faker->sentence($faker->numberBetween(6, 15)))
                    ->setReference(time() + $faker->numberBetween(1000) + $cours->getId())
                    ->setProposition4($faker->sentence($faker->numberBetween(6, 15)));
                $quiz->setPropositionJuste([$faker->numberBetween(1, 4)]);
                $quiz->setCours($cours);
                $em->persist($quiz);
            }
        }

        $em->flush();

        return $this->redirectToRoute('app_front');
    }

    #[Route('/send-mail', name: 'app_home_send_mail')]
    public function sendMail(MailerInterface $mailerInterface)
    {
        $email = (new Email())
            ->from('no-reply@kulmapeck.com')
            ->to('emmaberanger2@gmail.com')
            ->subject('Un test')
            ->text('Je verifie juste si les mails sont correctement transmis !')
            ->html("<p>Je verifie juste si les mails sont correctement transmis ! HTML</p>");

        try {
            dump($mailerInterface->send($email));
        } catch (TransportExceptionInterface $e) {
            dd($e);
        }

        dd("Message envoyé !");
    }
}
