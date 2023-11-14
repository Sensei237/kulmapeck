<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendAllUsersEmailService
{
    private $mailer;
    private $userRepository;
    private $parameterBag;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function send( $title, $body, User $user = null)
    {
        $users = ($user == null) ? $this->userRepository->findAll() : [$user];

        foreach ($users as $user) {
            $email = (new TemplatedEmail())
                ->from(new Address( 'no-reply@kulmapeck.com', 'Kulmapeck' ))
                ->to($user->getEmail())
                ->subject($title)
                ->htmlTemplate('emails/student-notifs.html.twig')
                ->context([
                    'user' => $user,
                    'content' => $body,
                ]);

            $this->mailer->send($email);

        }

    }
}
