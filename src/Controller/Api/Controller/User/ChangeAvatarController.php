<?php

namespace App\Controller\Api\Controller\User;

use App\Entity\Personne;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Description of ChangeAvatarController
 *
 * @author sensei
 */
#[AsController]
class ChangeAvatarController {
    
    public function __invoke(Personne $personne, Request $request): Personne
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
        // dd($uploadedFile = $request->files->get('file'));
        $personne->file = $uploadedFile;
        $personne->setUpdateAt(new \DateTimeImmutable());

        return $personne;
    }
}
