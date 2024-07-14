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
    
    public static function initierPaymentPlan(User $user, Abonnement $abonnement, ?PaymentMethod $paymentMethod, Keys $keys, string $referencePayment): array
    {
        $isPaied = false;

        $numeroTelephone = $user->getPersonne()->getTelephone();

        $requestData['transaction_amount'] = $abonnement->getMontant();
        $requestData['transaction_currency'] = 'XAF';
        $requestData['transaction_reason'] = 'Subscription';
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
        ];
    }

    public static function initierPayment(User $user, Cours $course, ?PaymentMethod $paymentMethod, Keys $keys, string $referencePayment): array
    {
        $isPaied = false;

        $numeroTelephone = $user->getPersonne()->getTelephone();

        $requestData['transaction_amount'] = $course->getMontantAbonnement();
        $requestData['transaction_currency'] = 'XAF';
        $requestData['transaction_reason'] = 'Achat Cours';
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
