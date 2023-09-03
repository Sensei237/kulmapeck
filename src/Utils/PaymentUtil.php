<?php

use App\Entity\Abonnement;
use App\Entity\Cours;
use App\Entity\PaymentMethod;
use App\Utils\ManageNetwork;

class PaymentUtil
{
    
    public static function initierPaymentPlan(Abonnement $abonnement, ?PaymentMethod $paymentMethod): bool
    {
        $isPaied = true;


        return $isPaied;
    }

    public static function initierPayment(Cours $course, ?PaymentMethod $paymentMethod): bool
    {
        $isPaied = true;


        return $isPaied;
    }
}
