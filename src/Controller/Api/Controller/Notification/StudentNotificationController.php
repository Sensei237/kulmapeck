<?php 

namespace App\Controller\Api\Controller\Notification;

use App\Entity\Eleve;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class StudentNotificationController extends AbstractController
{

    public function __construct(
        private NotificationRepository $notificationRepository
    ) {
    }

    public function __invoke(): array
    {
        return $this->notificationRepository->findBy([
                'destinataire' => $this->getUser()
            ], 
            ['createdAt' => 'DESC', 'isRead' => 'ASC']
        );
    }
}
