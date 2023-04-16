<?php
namespace App\EntityListener;

use App\Controller\RegistrationController;
use App\Entity\User;
use App\Repository\PersonneRepository;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserListener
{
    public function __construct(private PersonneRepository $personneRepository, 
        private UrlGeneratorInterface $urlGeneratorInterface,
        private EmailVerifier $emailVerifier,
        private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        
    }
    public function prePersist(User $user): void
    {
        if (!$user->getPersonne()->getInvitationCode()) {
            $codeInvitation = RegistrationController::generateInvitationCode($this->personneRepository);
            $invitationLink = json_encode([
                'trainer' => $this->urlGeneratorInterface->generate('app_front_register', ['type' => 'trainer', 'invitation' => $codeInvitation]),
                'student' => $this->urlGeneratorInterface->generate('app_front_register', ['type' => 'student', 'invitation' => $codeInvitation])
            ]);
            $user->getPersonne()->setInvitationCode($codeInvitation)
                ->setInvitationLink($invitationLink)
                ->setParent($this->personneRepository->findOneBy(['invitationCode' => $user->parentCode]));
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
        }

        if ($user->getEleve() && !$user->getEleve()->getReference()) {
            $user->getEleve()->setReference((time() + $user->getId() + $user->getEleve()->getId()) . '');
            $user->setRoles(['ROLE_STUDENT']);
        }

        if ($user->getEnseignant() && !$user->getEnseignant()->getReference()) {
            $user->getEnseignant()->setReference((time() + $user->getId() + $user->getEnseignant()->getId()) . '');
            $user->setRoles(['ROLE_INSTRUCTOR']);
        }

        $user->getPersonne()->setUtilisateur($user);
    }

    public function postPersist(User $user): void 
    {
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
    }

    public function preUpdate(User $user): void 
    {

    }
}
