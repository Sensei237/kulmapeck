<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PushNotificationService
{
    private $firebaseApiKey;
    private $cacert;

    public function __construct(ContainerBagInterface $parameterBag)
    {
        $this->firebaseApiKey = $parameterBag->get('kernel.project_dir') . '/config/Keys/pushNotificationsToken.pem';
        $this->cacert = $parameterBag->get('kernel.project_dir') . '/config/Keys/cacert.pem';
    }

    public function sendPushNotification($jsonData)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($jsonData),
            CURLOPT_CAINFO=>$this->cacert,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer AAAAHYNSk08:APA91bGxCkzdSV6UZNhHYA4ePw68R42s9kHh1SwEMHpl2Bx-8t8AQvb4J87q6tAdjIkq2zABxxSiAXjk8CknWpGs00mrnfPWo9U4ITejG2mn8YsrSboiLfkx9lNmLdGTx5UJ_kR-rOwo',
                'Content-Type: application/json',
                'crypto-key: p256ecdsa=BKID237QeQT_19xOmqybLN9KOC1_f3m_3xTQV8dRkzg9cJVj6T96D4tSwv630-hUiW5zl5_7m1FH8iQYEfFQftY'
            ]
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'ERRROOOr == '.curl_error($curl);
        }

        curl_close($curl);

        return  $response;
    }


    public function PushNotificationData($body,$title,$to='/topics/all')
    {
        $jsonData = [
            "notification" => [
                "body" => $body,
                "title" => $title,
                "icon" => "https://kulmapeck.com/uploads/images/enseignants/kyc/LOGO-KULMAPECK-1-64aed96a8e5f3.png",
                //"image" => "https://kulmapeck.com/uploads/images/enseignants/kyc/LOGO-KULMAPECK-1-64aed96a8e5f3.png"
            ],
            "priority" => "high",
            "data" => [
                "clickaction" => "FLUTTERNOTIFICATIONCLICK",
                "id" => uniqid(),
                "status" => "done"
            ],
            "to" => $to
        ];
        

        $response = $this->sendPushNotification($jsonData);
        
        return new JsonResponse($response);
    }
}
