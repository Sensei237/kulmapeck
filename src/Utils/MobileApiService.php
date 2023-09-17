<?php
namespace App\Utils;

use App\Utils\PayIn;
use App\Utils\PayOut;
use App\Utils\Utils;

final class MobileApiService
{
    /**
     * Methode qui gere des retraits d'argents du compte Kulmapeck vers ses clients
     * elle attend un objet payOut
     */
    public static function sendPayout(array $requestData, string $apiUrl, string $privateKey, $cacert)
    {
        $apiUrl .= 'out';
        $headers = [
            'Content-Type: application/json',
            'X-PRIVATE-KEY: ' . $privateKey,
        ];

        $checkNumber = Utils::checkNumberOperator($requestData['customer_phone_number']);
        if ($checkNumber['hasError']) {
            return ['responseData' => null, 'error' => true, 'message' => $checkNumber['message']]; 
        }
        // Create a PayOut object using the constructor
        $payOut = new PayOut(
            $requestData['transaction_amount'],
            $requestData['transaction_currency'],
            $requestData['transaction_reason'],
            $requestData['app_transaction_ref'],
            $requestData['customer_phone_number'], //client
            $requestData['customer_name'],
            $requestData['customer_email'],
            $checkNumber['code'],
            $requestData['customer_lang'],
            $requestData['transaction_receiver'], //client

        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CAINFO, $cacert);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            return ['errorList' => $error, 'error' => true];
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 201) {
            return ['responseData' => $responseData, 'error' => false, 'message' => 'done'];

        } else {
            return ['responseData' => $responseData, 'error' => true, 'message' => 'Impossible de traiter la demande pour le moment !'];
        }

    }

    /**
     * Methode qui gère les paiements
     */
    public static function sendPayIn(array $requestData, string $apiUrl, string $privateKey, $cacert)
    {
        $apiUrl .= 'in';
        $headers = [
            'Content-Type: application/json',
            'X-PRIVATE-KEY: ' . $privateKey,
        ];

        $checkNumber = Utils::checkNumberOperator($requestData['customer_phone_number']);
        if ($checkNumber['hasError']) {
            return ['responseData' => null, 'error' => true, 'message' => $checkNumber['message']]; 
        }

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
            $checkNumber['code'],
        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payOut->toJson());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CAINFO, $cacert);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 202) {
            return ['responseData' => $responseData, 'error' => false, 'message' => 'Done'];
        } else {
            return ['responseData' => $responseData, 'error' => true, 'message' => "Impossible de traiter la demande pour le moment !"];
        }
    }
}
