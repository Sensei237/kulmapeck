<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\AuthenticationToken;
use Symfony\Component\Security\Core\Role\Role;


#[Route('/google')]
class GoogleController extends AbstractController
{
    
    
    #[Route('/login', name: 'app_google')]
    public function login(Request $request)
    {
        $resourceOwner = $this->get('hwi_oauth.resource_owner.google');

        // Redirect the user to Google to authenticate
        return $resourceOwner->authorizationService()->requestAccess('code');
    }

    #[Route('/callback', name: 'app_google_callback')]
    public function callback(Request $request)
    {
        $resourceOwner = $this->get('hwi_oauth.resource_owner.google');

        // Get the user's authentication credentials
        $credentials = $resourceOwner->authorizationService()->getAccessToken(
            $request->query->get('code')
        );

        // Check the user's authentication credentials
        // ...

        // Log the user in to your application
        $this->get('security.token_storage')->setToken(
            new AuthenticationToken(
                $user,
                'google',
                new RoleCollection([new Role('ROLE_USER')])
            )
        );

        // Redirect the user to the homepage
        return $this->redirectToRoute('app_homepage');
    }
}
