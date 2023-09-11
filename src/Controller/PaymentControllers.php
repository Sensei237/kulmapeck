<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\PaymentRepository;
use App\Repository\RetraitRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/api/pay')]
class PaymentControllers extends AbstractController
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
    // #[Route('/out', name: 'app_payment_out', methods: 'POST')]
    // public function sendPayout(Request $request)
    // {
    //     $requestData = json_decode($request->getContent(), true);
    //     $apiUrl = $this->apiUrl . 'out';
    //     $headers = [
    //         'Content-Type: application/json',
    //         'X-PRIVATE-KEY: ' . $this->privateKey,
    //     ];
    //     // Create a PayOut object using the constructor
    //     $payOut = new PayOut(
    //         $requestData['transaction_amount'],
    //         $requestData['transaction_currency'],
    //         $requestData['transaction_reason'],
    //         $requestData['app_transaction_ref'],
    //         $requestData['customer_phone_number'], //client
    //         $requestData['customer_name'],
    //         $requestData['customer_email'],
    //         Utils::checkNumberOperator($requestData['customer_phone_number']),
    //         $requestData['customer_lang'],
    //         $requestData['transaction_receiver'], //client

    //     );

    //     $ch = curl_init($apiUrl);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_CAINFO, $this->cacert);

    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //     if (curl_errno($ch)) {
    //         $error = curl_error($ch);
    //         var_dump($error);
    //     }
    //     curl_close($ch);

    //     $responseData = json_decode($response, true);

    //     if ($httpCode === 201) {
    //         // if accepted  get Payment Url

    //         return new JsonResponse([$responseData, 'error' => false]);
    //         //return $this->redirect($paymentUrl);

    //     } else {
    //         // get error
    //         /*
    //             {"status":
    //               ,"message":
    //             }
    //         */
    //         return new JsonResponse($responseData);
    //     }

    // }

    /**
     * Méthode qui gère les paiements d'argent des clients vers le compte Kulmapeck.
     * Elle attend un objet Payin.
     * En cas de succès, le client est automatiquement redirigé vers une route de paiement,
     * et une fonction de rappel sera envoyée pour donner le statut du paiement quelques secondes après.
     */

    // #[Route('/in', name: 'app_payment_in', methods: 'POST')]
    // public function sendPayIn(Request $request)
    // {
    //     $requestData = json_decode($request->getContent(), true);
    //     $apiUrl = $this->apiUrl . 'in';
    //     $headers = [
    //         'Content-Type: application/json',
    //         'X-PRIVATE-KEY: ' . $this->privateKey,
    //     ];
    //     // Create a PayOut object using the constructor
    //     $payOut = new PayIn(
    //         $requestData['transaction_amount'],
    //         $requestData['transaction_currency'],
    //         $requestData['transaction_reason'],
    //         $requestData['app_transaction_ref'],
    //         $requestData['customer_phone_number'],
    //         $requestData['customer_name'],
    //         $requestData['customer_email'],
    //         $requestData['customer_lang'],
    //         $requestData['transaction_receiver'], //Kulmapeck
    //         Utils::checkNumberOperator($requestData['customer_phone_number']),
    //     );

    //     $ch = curl_init($apiUrl);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_CAINFO, $this->cacert);

    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //     curl_close($ch);

    //     $responseData = json_decode($response, true);

    //     if ($httpCode === 201) {
    //         // success and get Payment Url
    //         if (isset($responseData['payment_url'])) {
    //             $paymentUrl = $responseData['payment_url'];
    //             $transactionRef = $responseData['transaction_ref'];
    //             $status = $responseData['status'];
    //             return $this->redirect($paymentUrl);
    //         }
    //     } else {
    //         // get error  
    //         /*
    //             {"status":
    //               ,"message":
    //             }
    //         */
    //         return $this->json($responseData);
    //     }


    // }


    /**
     * Fonction de rappel permettant de mettre a jour le statut de la transaction effetuée
     * elle est exécutée automatiquement par le serveur distant à intervalle regulier de 5 min
     */
    #[Route('/callback', name: 'app_payment_callback', methods: 'GET')]
    public function handleCallback(Request $request, UserRepository $userRepository, NetworkConfigRepository $networkConfigRepository, EleveRepository $eleveRepository, PaymentRepository $paymentRepository, RetraitRepository $retraitRepository, EntityManagerInterface $em)
    {
        // Check if Kulmapeck  sender's IP address
        $senderIp = $request->getClientIp();
        $expectedIp = '192.162.71.169';

        if ($senderIp !== $expectedIp) {
            throw new InvalidArgumentException("Unauthorized sender IP : " . $senderIp);
        }

        // Get parameters from the URL
        $transactionRef = $request->query->get('transaction_ref');
        $status = $request->query->get('status');
        // Now you can use $transactionRef and $status as needed

        $payment = $paymentRepository->findOneBy(['transactionReference' => $transactionRef]);
        if ($payment !== null) {
            $payment->setStatus($status)
                ->setIsExpired(false);
            if ($payment->getAbonnement() !== null&& strtoupper($status) == 'SUCCESS') {
                $payment->getEleve()->setIsPremium(true);
                $eleveRepository->save($payment->getEleve());
            }
            $paymentRepository->save($payment, true);

            // On gère la distribution des points pour le reseau
            $eleve = $payment->getEleve();
            if ($eleve !== null) {
                // On cherche tous les payments effectués par l'eleve et qui ont abouti
                $payments = $paymentRepository->findBy(['eleve' => $eleve, 'status' => $status]);
                // S'il a moins de deux payments abouti alors on cherche à partager les points
                if (count($payments) < 2) {
                    $networkConfig = $networkConfigRepository->findOneBy([]);
                    if ($networkConfig !== null) {
                        ManageNetwork::manage($eleve->getUtilisateur(), $networkConfig, $userRepository, $em);
                    }
                }
            }
            
        }else {
            $retrait = $retraitRepository->findOneBy(['transactionReference' => $transactionRef]);
            if ($retrait !== null) {
                $retrait->setStatus($status);
                $retraitRepository->save($retrait, true);
            }
        }

        // Return a response if needed
        return new Response('Callback received successfully'.$transactionRef.'statut' .$status);
    }
    #[Route('/email', name: 'balance', methods: ['POST'])]
    public function emailSender(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('no-reply@kulmapeck.com')
            ->to("ondouabenoit392@gmail.com")
            ->subject("Demande rejeter")
            ->text("motif")
            ->html("<p>" . "motif" . "</p>");

        // Send the email
        if ($mailer->send($email)) {
            return new JsonResponse('Email sent successfully!');
        } else {
            return new JsonResponse('Email could not be sent. Mailer Error: ');
        }
    }

}