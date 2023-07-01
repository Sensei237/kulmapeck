<?php
namespace App\Controller\Api\Controller\Course\Chapter;

use App\Entity\Chapitre;
use App\Repository\EleveRepository;
use App\Repository\QuizResultRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ShowQuizzesResultList extends AbstractController 
{
    public function __construct(
        private QuizResultRepository $quizResultRepository, 
        private Security $security, 
        private EleveRepository $eleveRepository)
    {
        
    }
    
    public function __invoke(Chapitre $chapitre)
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            return new \ArrayObject(['error' => true, 'message' => 'Vous devez etre élève']);
        }

        $quizzesResults = $this->quizResultRepository->findStudentQuizResultsByCourseOrChapter($eleveConnected, null, $chapitre);
        
        $notes = 0;
        
        foreach ($quizzesResults as $qr) {
            $notes += $qr->getNote();
        }

        $isValidated = $notes >= 14;


        return new \ArrayObject([
            'quizzesResults' => $quizzesResults,
            'notes' => number_format($notes, 2, ',') . ' / 20',
            'isValidated' => $isValidated 
        ]);
    }
}
