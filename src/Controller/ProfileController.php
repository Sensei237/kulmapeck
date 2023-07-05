<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Personne;
use App\Form\PersonneFormType;
use App\Repository\EleveRepository;
use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\FileUploader;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    
    #[Route('/student/profile', name: 'app_student_profile')]
    #[Route('/instructor/profile', name: 'app_instructor_profile')]
    #[Route('/admin/profile', name: 'app_profile')]
    public function index(EleveRepository $eleveRepository, PersonneRepository $personneRepository): Response
    {
        $personne = $personneRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if (!$personne) {
            throw $this->createAccessDeniedException();
        }

        $eleve = $personne->getUtilisateur()->getEleve();
        $enseignant = $personne->getUtilisateur()->getEnseignant();

        $template = "profile/index.html.twig";

        if ($eleve !== null) {
            $postUri = $this->generateUrl('app_student_profile_edit');
        } elseif ($enseignant !== null) {
            $postUri = $this->generateUrl('app_instructor_profile_edit');
        } else {
            $postUri = $this->generateUrl('app_profile_edit');
            $template = "profile/admin.html.twig";
        }

        $personneForm = $this->createForm(PersonneFormType::class, $personne, [
            'action' => $postUri,

        ]);

        return $this->render($template, [
            'isProfile' => true,
            'student' => $eleve,
            'personneForm' => $personneForm->createView(),
            'personne' => $personne,
            'enseignant' => $enseignant
        ]);
    }

    #[Route('/student/profile/edit', name: 'app_student_profile_edit', methods: ['POST'])]
    #[Route('/instructor/profile/edit', name: 'app_instructor_profile_edit', methods: ['POST'])]
    #[Route('/admin/profile/edit', name: 'app_profile_edit', methods: ['POST'])]
    public function editPersonnalInformations(Request $request, PersonneRepository $personneRepository, EleveRepository $eleveRepository, FileUploader $fileUploader): Response
    {
        $personne = $personneRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if (!$personne) {
            throw $this->createAccessDeniedException();
        }

        $eleve = $personne->getUtilisateur()->getEleve();
        $enseignant = $personne->getUtilisateur()->getEnseignant();

        if ($eleve !== null) {
            $postUri = $this->generateUrl('app_student_profile_edit');
            $path = 'images/eleves';
            $redirectUri = 'app_student_profile';
        } elseif ($enseignant !== null) {
            $postUri = $this->generateUrl('app_instructor_profile');
            $path = 'images/enseignants/kyc';
            $redirectUri = 'app_instructor_profile';
        }else {
            $postUri = $this->generateUrl('app_profile_edit');
            $path = 'images/admin';
            $redirectUri = 'app_profile';
        }

        $personneForm = $this->createForm(PersonneFormType::class, $personne, [
            'action' => $postUri,

        ]);
        $personneForm->handleRequest($request);

        if ($personneForm->isSubmitted() && $personneForm->isValid()) {
            $this->uploadImagesFiles($personne, $fileUploader, $path);
            $personneRepository->save($personne, true);

            $this->addFlash('success', 'Your personnal informations was updated');

            return $this->redirectToRoute($redirectUri);
        }

        return $this->render('profile/index.html.twig', [
            'isProfile' => true,
            'student' => $eleve,
            'personneForm' => $personneForm->createView(),
            "personne" => $personne,
            'enseignant' => $enseignant,
        ]);
    }

    #[Route('/student/profile/change-email', name: 'app_student_profile_change_email', methods: ['POST'])]
    #[Route('/instructor/profile/change-email', name: 'app_instructor_profile_change_email', methods: ['POST'])]
    #[Route('/admin/profile/change-email', name: 'app_profile_change_email', methods: ['POST'])]
    public function changeEmail(Request $request, UserRepository $userRepository, PersonneRepository $personneRepository, EleveRepository $eleveRepository): Response 
    {
        $personne = $personneRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if (!$personne) {
            throw $this->createAccessDeniedException();
        }

        $eleve = $personne->getUtilisateur()->getEleve();
        $enseignant = $personne->getUtilisateur()->getEnseignant();

        $user = $userRepository->findOneBy(['email' => $request->request->get('current_email')]);

        if ($user === null || $user->getEmail() !== $personne->getUtilisateur()->getEmail()) {
            $this->addFlash('danger', "L'email " . $request->request->get('current_email') . " ne correspond à aucun n'utilisateur ou est reservé à quelqu'un d'autre");
            
            return $this->redirectToRoute('app_student_profile');
        }

        $user->setEmail($request->request->get('new_email'))->setIsVerified(false);
        $userRepository->save($user, true);

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from('no-reply@kulmapeck.com')
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->addFlash('notification', "Votre adresse mail a été modifiée ! Un mail voua a été envoyé pour confirmer que c'est vous !");

        return $this->redirectToRoute('app_logout');
    }

    private function uploadImagesFiles(Personne $personne, FileUploader $fileUploader, $path = 'images/eleves')
    {
        // dd($fileName);
        if ($personne->getImageFile() != null) {

            $fileName = $fileUploader->upload($personne->getImageFile(), $path);
            $personne->setAvatar($fileName);
        }
    }
}
