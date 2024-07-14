<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\Forum;
use App\Entity\Sujet;
use App\Repository\EleveRepository;
use App\Repository\MembreRepository;
use App\Service\SendAllUsersEmailService;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Repository\SujetRepository;

class PostSubjectController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private MembreRepository $membreRepository,
        private SendAllUsersEmailService $sendAllUsersEmailService,
        private SujetRepository $sujetRepository,
        
    )
    {

    }

    public function __invoke(Forum $forum, Request $request)
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new BadRequestHttpException("Vous devez être connecté", null, 403);
        }

        $membre = $this->membreRepository->findOneBy(['utilisateur' => $user]);
        if (!$membre || !$membre->getForums()->contains($forum)) {
            throw new BadRequestException("Vous ne pouvez pas écrire dans ce forum");
        }


           $content = $request->getContent();
           $contentData = json_decode($content, true);
           $content = $contentData['content'];
             $title='Nouveu  sujet << '.$content.' >>';
            $body='Chere enseignant, le sujet : [ '.$content.' ] pour le cour de ' .$forum->getCours()->getIntitule().' dont vous etes le guide vient de voir le jour  
            . Merci de bien consulter le soucis de cet apprenant ';
            
            $sujet = new Sujet();
            $sujet->setForum($forum);
            $sujet->setMembre($membre)
            ->setContent($content)
                ->setReference((time() + $membre->getId()) * 2);

              $this->sujetRepository->save($sujet, true);
              $user= $forum->getCours()->getEnseignant()->getUtilisateur();
              $this->sendAllUsersEmailService->sendManyRecipients( $title, $body,$user);
           
         
    }
}