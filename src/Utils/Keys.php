<?php

namespace App\Utils;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Keys
{

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getPrivateKey()
    {
        $private = $this->parameterBag->get('kernel.project_dir') . '/config/Keys/private.pem';
        $privateContent = file_get_contents($private);
        if ($privateContent === false) {
            return;
        }
        return $privateContent;
    }

    public function getCacert()
    {
        return $this->parameterBag->get('kernel.project_dir') . '/config/Keys/cacert.pem';

    }

    public function getApiUrl(): string
    {
        return 'https://pay-kulmapeck.online/api/pay/';
    }
}