<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationStudentType;
use App\Form\RegistrationTeacherType;
use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_front_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader, PersonneRepository $personneRepository): Response
    {
        $userType = $request->get('type');

        $user = new User();
        $user->parentCode = $request->get('invitation');
        $accountType = 0;

        if (strtolower($userType) === 'student') {
            $form = $this->createForm(RegistrationStudentType::class, $user);
            $accountType = 0;
        } elseif (strtolower($userType) === 'trainer') {
            $form = $this->createForm(RegistrationTeacherType::class, $user);
            $accountType = 1;
        } else {
            throw $this->createNotFoundException();
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            
            $codeInvitation = self::generateInvitationCode($personneRepository);
            $invitationLink = json_encode([
                'trainer' => 'https://kulmapeck.com' . $this->generateUrl('app_front_register', ['type' => 'trainer', 'invitation' => $codeInvitation]),
                'student' => 'https://kulmapeck.com' . $this->generateUrl('app_front_register', ['type' => 'student', 'invitation' => $codeInvitation])
            ]);
            $user->getPersonne()->setInvitationCode($codeInvitation)
                ->setInvitationLink($invitationLink)
                ->setParent($personneRepository->findOneBy(['invitationCode' => $form->get('parentCode')->getData()]));

            if($accountType == 1) {
                $path = 'images/enseignants/kyc';
                $user->getEnseignant()->setReference((time() + $user->getId() + $user->getEnseignant()->getId()) . '');
            } elseif ($accountType == 0) {
                $path = 'images/eleves';
                $user->getEleve()->setReference((time() + $user->getId() + $user->getEleve()->getId()) . '');
            }else {
                $path = 'images/admin';
            }
            // On upload tous les fichiers contenus dans le formulaire
            $this->uploadImagesFiles($user, $fileUploader, $path);

            $user->getPersonne()->setUtilisateur($user);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@kulmapeck.com', 'Kulmapeck'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $this->addFlash('success', "Un lien a été envoyer dans votre adresse mail pour activer votre compte");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'userType' => $userType,
            'invitation' => $request->query->get('invitation'),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            $id = $request->query->get('id');
            $user = $userRepository->find($id);
        }
        
        if ($user === null) {
            return $this->redirectToRoute('app_home');
        }
        
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }

    private function uploadImagesFiles(User $user, FileUploader $fileUploader, $path = 'images/enseignants/kyc')
    { 
        // dd($user->getPersonne());
        if ($user->getPersonne()->getImageFile() != null) {

            $fileName = $fileUploader->upload($user->getPersonne()->getImageFile(), $path);
            $user->getPersonne()->setAvatar($fileName);
        }

        if ($user->getEnseignant() !== null) {
            $user->getEnseignant()->setDiplome($fileUploader->upload($user->getEnseignant()->diplomeFile, $path))
                ->setRectoCNI($fileUploader->upload($user->getEnseignant()->rectoCNIFile, $path))
                ->setVersoCNI($fileUploader->upload($user->getEnseignant()->versoCNIFile, $path))
                ->setSelfieCNI($fileUploader->upload($user->getEnseignant()->selfieCNIFile, $path))
                ->setEmploiDuTemps($fileUploader->upload($user->getEnseignant()->emploiDuTempsFile, $path));
            $user->setRoles(['ROLE_INSTRUCTOR']);
        }elseif ($user->getEleve() !== null) {
            $user->setRoles(['ROLE_STUDENT']);
        }

        return $user;
    }

    public static function generateInvitationCode(PersonneRepository $pr)
    {
        $tab = ['A', 'Z', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'Q', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'W', 'X', 'C', 'V', 'B', 'N', '1','2', '3', '4', '5', '6', '7', '8', '9', '0'];
        $exist = true;
        $code = '';
        while ($exist) {
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $tab[rand(0, count($tab) - 1)];
            }
            $p = $pr->findOneBy(['invitationCode' => $code]);
            if (is_null($p)) {
                $exist = false;
            }
        }

        return $code;
    }
}
