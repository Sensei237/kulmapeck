<?php
namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\Sujet;
use App\Repository\ForumMessageRepository;
use ArrayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SubjectMessages extends AbstractController 
{
    public function __construct(private ForumMessageRepository $forumMessageRepository)
    {
        
    }

    public function __invoke(Sujet $sujet)
    {
        $messagesListe = $this->forumMessageRepository->findBy(['sujet' => $sujet, 'isAnswer' => false]);
        $messages = [];
        foreach ($messagesListe as $m) {
            $messages[] = [
                'messages' => $m,
                'responses' => $this->forumMessageRepository->findBy(['sujet' => $sujet, 'isAnswer' => false, 'forumMessage' => $m])
            ];
        }
        
        return new ArrayObject([
            'sujet' => $sujet,
            'messges' => $messages
        ]);
    }
}
