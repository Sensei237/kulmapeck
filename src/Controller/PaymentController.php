<?php
namespace App\Controller;

use ApiPlatform\OpenApi\Model\Response;
use App\Utils\Keys;
use App\Utils\PayIn;
use App\Utils\PayOut;
use App\Utils\Utils;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/api/pay')]
class PaymentController extends AbstractController
{

    private $privateKey;

    private $cacert;

    private $apiUrl;


    public function __construct(Keys $apiKeys)
    {
        $this->privateKey = $apiKeys->getPrivateKey();
        $this->cacert = $apiKeys->getCacert();
        //$this->apiUrl = $_ENV['API_PAY_URL'];
        $this->apiUrl = 'https://pay-kulmapeck.online/api/pay/';

    }

    /**
     * Methode qui gere des retraits d'argents du compte Kulmapeck vers ses clients
     * elle attend un objet payOut
     */
    #[Route('/out', name: 'app_payment_out', methods: 'POST')]
    public function sendPayout(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $apiUrl = $this->apiUrl . 'out';
        $headers = [
            'Content-Type: application/json',
            'X-PRIVATE-KEY: ' . $this->privateKey,
        ];
        // Create a PayOut object using the constructor
        $payOut = new PayOut(
            $requestData['transaction_amount'],
            $requestData['transaction_currency'],
            $requestData['transaction_reason'],
            $requestData['app_transaction_ref'],
            $requestData['customer_phone_number'], //client
            $requestData['customer_name'],
            $requestData['customer_email'],
            Utils::checkNumberOperator($requestData['customer_phone_number']),
            $requestData['customer_lang'],
            $requestData['transaction_receiver'], //client

        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CAINFO, $this->cacert);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            var_dump($error);
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 201) {
            // if accepted  get Payment Url

            return new JsonResponse([$responseData, 'error' => false]);
            //return $this->redirect($paymentUrl);

        } else {
            // get error
            /*
                {"status":
                  ,"message":
                }
            */
            return new JsonResponse($responseData);
        }

    }

    /**
     * Méthode qui gère les paiements d'argent des clients vers le compte Kulmapeck.
     * Elle attend un objet Payin.
     * En cas de succès, le client est automatiquement redirigé vers une route de paiement,
     * et une fonction de rappel sera envoyée pour donner le statut du paiement quelques secondes après.
     */

    #[Route('/in', name: 'app_payment_in', methods: 'POST')]
    public function sendPayIn(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $apiUrl = $this->apiUrl . 'in';
        $headers = [
            'Content-Type: application/json',
            'X-PRIVATE-KEY: ' . $this->privateKey,
        ];
        // Create a PayOut object using the constructor
        $payOut = new PayIn(
            $requestData['transaction_amount'],
            $requestData['transaction_currency'],
            $requestData['transaction_reason'],
            $requestData['app_transaction_ref'],
            $requestData['customer_phone_number'],
            $requestData['customer_name'],
            $requestData['customer_email'],
            $requestData['customer_lang'],
            $requestData['transaction_receiver'], //Kulmapeck
            Utils::checkNumberOperator($requestData['customer_phone_number']),
        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CAINFO, $this->cacert);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 201) {
            // success and get Payment Url

            if (isset($responseData['payment_url'])) {
                $paymentUrl = $responseData['payment_url'];
                return $this->redirect($paymentUrl);
            }
        } else {
            // get error  
            /*
                {"status":
                  ,"message":
                }
            */
            return $this->json($responseData);
        }


    }


    /**
     * Fonction de rappel permettant de mettre a jour le statut de la transaction effetuée
     * elle est exécutée automatiquement par le serveur distant à intervalle regulier de 5 min
     */
    #[Route('/recall', name: 'app_payment_callback', methods: 'GET')]
    public function handleCallback(Request $request)
    {
        // Check if Kulmapeck  sender's IP address
        $senderIp = $request->getClientIp();
        $expectedIp = '192.162.71.169';

        if ($senderIp !== $expectedIp) {
            throw new InvalidArgumentException("Unauthorized sender IP : ".$senderIp);
        }

        // Get parameters from the URL
        $transactionRef = $request->query->get('transaction_ref');
        $status = $request->query->get('status');

        // Now you can use $transactionRef and $status as needed

        // Return a response if needed
        return new Response('Callback received successfully');
    }
}