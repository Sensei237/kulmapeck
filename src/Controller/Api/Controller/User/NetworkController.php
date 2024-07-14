<?php

namespace App\Controller\Api\Controller\User;

use App\Entity\Personne;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Description of ChangeAvatarController
 *
 * @author sensei
 */
#[AsController]
class NetworkController extends AbstractController
{
    public function __construct(
        private PaginatorInterface $paginatorInterface
    ) {
    }

    public function __invoke(Personne $personne, Request $request): PaginationInterface
    {
        // dd($request->query);
        $user = $this->getUser();
        if ($user === null || !$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        return $this->paginatorInterface->paginate($personne->getInvites(), $request->query->getInt('page', 1), 10);
    }
}
