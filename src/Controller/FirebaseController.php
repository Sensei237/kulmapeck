<?php

namespace App\Controller;

use App\Entity\Device;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[ Route( '/api/firebase' ) ]

class FirebaseController extends AbstractController {

    private $entityManager;
    private $security;

    public function __construct( EntityManagerInterface $entityManager, Security $security ) {
        $this->security = $security;

        $this->entityManager = $entityManager;
    }
    #[ Route( '/token', methods: [ 'POST' ] ) ]

    public function saveNewToken( Request $request ) {
        $content = $request->getContent();

        // Decode the JSON content
        $data = json_decode( $content, true );

        $token = $data[ 'token' ];
        $user = $this->security->getUser();


        $existingDevice = $this->entityManager->getRepository( Device::class )->findOneBy( [
            'deviceToken' => $user,
            'token' => $token,
        ] );

        if ( $existingDevice ) {
            $updatedAt = new \DateTimeImmutable( 'now' );
            $existingDevice->setUpdatedAt( $updatedAt );

            $this->entityManager->flush();

            return new Response( 'Device with the given token already exists for the user' );
        }

        // Create a new Device instance
        $device = new Device();

        // Set the device token
        $device->setDeviceToken( $user );
        $device->setToken( $token );
        $createdAt = new \DateTimeImmutable( 'now' );
        $device->setCreatedAt( $createdAt );

        // Persist the entity
        $this->entityManager->persist( $device );

        // Flush changes to the database
        $this->entityManager->flush();

        return new Response( 'Token saved successfully' );
    }

}
