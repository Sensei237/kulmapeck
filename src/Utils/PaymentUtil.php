<?php
namespace App\Utils;

use App\Entity\Abonnement;
use App\Entity\Cours;
use App\Entity\PaymentMethod;
use App\Entity\User;
use App\Utils\Keys;

/**
 * Dans ce controller, nous allons gerer le paiement des abonnements et le paiement des cours
 */
class PaymentUtil
{
    /**
     * Cette fonction est appelee lorsque l'eleve veut payer un abonnement
     */
    public static function initierPaymentPlan(User $user, Abonnement $abonnement, ?PaymentMethod $paymentMethod, Keys $keys, string $referencePayment, string $phoneNumber): array
    {
        $isPaied = false;

        $numeroTelephone = $phoneNumber;

        $requestData['transaction_amount'] = $abonnement->getMontant();
        $requestData['transaction_currency'] = 'XAF';
        $requestData['transaction_reason'] = 'Subscription : '.$abonnement->getLabel();
        $requestData['app_transaction_ref'] = $referencePayment;
        $requestData['customer_phone_number'] = $numeroTelephone; //client
        $requestData['customer_name'] = $user->getPersonne()->getNomComplet();
        $requestData['customer_email'] = $user->getEmail();
        $requestData['customer_lang'] = 'fr';
        $requestData['transaction_receiver'] = $numeroTelephone; //client

        $response = MobileApiService::sendPayIn(
            $requestData, 
            $keys->getApiUrl(), 
            $keys->getPrivateKey(), 
            $keys->getCacert()
        );

        if (!$response['error']) {
            $isPaied = true;
        }

        return [
            'isPaied' => $isPaied, 
            'responseData' => $response['responseData'],
            'response' => $response,
        ];
    }

    /**
     * Cette fonction est appelée lorsque l'eleve essaie de payer un cours
     */
    public static function initierPayment(User $user, Cours $course, ?PaymentMethod $paymentMethod, Keys $keys, string $referencePayment, string $phoneNumber): array
    {
        $isPaied = false;

        $numeroTelephone = $phoneNumber;

        $requestData['transaction_amount'] = $course->getMontantAbonnement();
        $requestData['transaction_currency'] = 'XAF';
        $requestData['transaction_reason'] = 'Achat Cours : ' . $course->getIntitule();
        $requestData['app_transaction_ref'] = $referencePayment;
        $requestData['customer_phone_number'] = $numeroTelephone; //client
        $requestData['customer_name'] = $user->getPersonne()->getNomComplet();
        $requestData['customer_email'] = $user->getEmail();
        $requestData['customer_lang'] = 'fr';
        $requestData['transaction_receiver'] = $numeroTelephone;

        $response = MobileApiService::sendPayIn(
            $requestData, 
            $keys->getApiUrl(), 
            $keys->getPrivateKey(), 
            $keys->getCacert()
        );

        if (!$response['error']) {
            $isPaied = true;
        }

        return [
            'isPaied' => $isPaied, 
            'responseData' => $response['responseData'],
            'response' => $response
        ];
    }
}
