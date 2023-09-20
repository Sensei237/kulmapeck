<?php
namespace App\Controller;

use App\Entity\Notification;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\NotificationRepository;
use App\Repository\PaymentRepository;
use App\Repository\RetraitRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use App\Utils\PayIn;
use App\Utils\PayOut;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\map;

#[Route('/pay')]
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
        $this->apiUrl = 'https://pay-kulmapeck.online/pay/';

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
    public function handleCallback(Request $request, NotificationRepository $notificationRepository, UserRepository $userRepository, NetworkConfigRepository $networkConfigRepository, EleveRepository $eleveRepository, PaymentRepository $paymentRepository, RetraitRepository $retraitRepository, EntityManagerInterface $em)
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

        $payment = $paymentRepository->findOneBy(['transactionReference' => $transactionRef]);
        if ($payment !== null && strtoupper($status) == 'SUCCESS') {
            $eleve = $payment->getEleve();
            $payment->setStatus($status)
                ->setIsExpired(false);
            if ($payment->getAbonnement() !== null) {
                $payment->getEleve()->setIsPremium(true);
                $eleveRepository->save($payment->getEleve());
            }elseif ($payment->getCours() !== null) {
                $eleve->addCour($payment->getCours());
            }
            $paymentRepository->save($payment, true);

            $notification = new Notification();
            $notification->setDestinataire($payment->getEleve()->getUtilisateur())
                ->setTitle("Payment effectué avec succès");
            if($payment->getCours() !== null) {
                $content = "Votre paiement pour l'achat du cours intitulé " . $payment->getCours()->getIntitule() . " a été accepté. Le cours figure desormais dans votre tableau de bord et vous pouvez le lire à tout moment.";
            }elseif ($payment->getAbonnement() !== null) {
                $content = "Votre souscription au plan " . $payment->getAbonnement()->getLabel() . " a été approuvé. Vous avez ainsi la possibilité de consulter toutes les ressources de notre plateforme pour une durée de " . $payment->getAbonnement()->getDuree() . " mois";
            }
            else {
                $content = "Le payement a été approuvé";
            }
            $notification->setContent($content)->setType(1);
            $notificationRepository->save($notification, true);

            // On gère la distribution des points pour le reseau
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
            
        }
        elseif ($payment !== null) {
            $payment->setStatus($status)
                ->setIsExpired(false);
            $paymentRepository->save($payment, true);
        }
        else{
            $retrait = $retraitRepository->findOneBy(['transactionReference' => $transactionRef]);
            if ($retrait !== null && strtoupper($status) == 'SUCCESS') {
                $retrait->setStatus($status);
                $retraitRepository->save($retrait, true);
            }
        }

        // Return a response if needed
        return new Response('Callback received successfully');
    }
}