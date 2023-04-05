<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationStudentType;
use App\Form\RegistrationTeacherType;
use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/admin/users')]
class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/', name: 'app_admin_registration')]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginatorInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('app_admin_registration_register')
        ]);

        $users = $userRepository->findBy(['isAdmin' => true]);

        return $this->render('admin/registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'users' => $paginatorInterface->paginate($users, $request->query->getInt('page', 1), 10),
            'rec' => true,
            'form' => $form->createView()
        ]);
    }

    #[Route('/register', name: 'app_admin_registration_register', methods: ['POST', 'GET'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader, PersonneRepository $personneRepository): Response
    {

        $user = new User();
        $user->parentCode = $request->get('invitation');
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            $codeInvitation = $this->generateInvitationCode($personneRepository);
            $invitationLink = json_encode([
                'trainer' => $this->generateUrl('app_front_register', ['type' => 'trainer', 'invitation' => $codeInvitation]),
                'student' => $this->generateUrl('app_front_register', ['type' => 'student', 'invitation' => $codeInvitation])
            ]);
            $user->getPersonne()->setInvitationCode($codeInvitation)
                ->setInvitationLink($invitationLink);

            $path = 'images/admin';
            // On upload tous les fichiers contenus dans le formulaire
            $this->uploadImagesFiles($user, $fileUploader, $path);

            $user->getPersonne()->setUtilisateur($user);
            $user->addRole('ROLE_ADMIN')->setIsAdmin(true);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('test@mail.com', 'Kulmapeck'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'rec' => true,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
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
    }

    public function generateInvitationCode(PersonneRepository $pr)
    {
        $tab = ['A', 'Z', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'Q', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'W', 'X', 'C', 'V', 'B', 'N', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
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

    #[Route('/{id}', name: 'app_admin_registration_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_registration', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/block', name: 'app_admin_registration_block', methods: ['GET'])]
    #[Route('/{id}/unblock', name: 'app_admin_registration_unblock', methods: ['GET'])]
    public function block(Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsBlocked(!$user->isIsBlocked());
        $userRepository->save($user, true);

        return $this->redirectToRoute('app_admin_registration', [], Response::HTTP_SEE_OTHER);
    }
}