<?php

namespace App\Utils;

use App\Entity\Abonnement;
use App\Entity\NetworkConfig;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ManageNetwork
{
    /**
     * Cette methode permet de distribuer les points
     * Elle est appelee lorsque l'utilisateur valide son compte (pour l'enseignant) et
     * lorsque l'etudiant paie un abonnement
     */
    public static function manage(User $user, NetworkConfig $networkConfig,
        UserRepository $userRepository, EntityManagerInterface $em, Abonnement $abonnement): array {
        if (!$user->isVerified()) {
            return [
                'hasDone' => false,
                'message' => "Impossible d'effectuer cette opération car votre compte n'est pas activé.",
            ];
        }

        $personne = $user->getPersonne();
        $eleve = $user->getEleve();

        if ($eleve === null) {
            return [
                'hasDone' => false,
                'message' => "Vous n'êtes ni élève ni enseignant.",
            ];
        }

        // if ($eleve !== null && !$eleve->isIsPremium()) {
        //     return [
        //         'hasDone' => false,
        //         'message' => "Vous n'êtes devez d'abord souscrire à un abonnement."
        //     ];
        // }

        $cmp = 1;
        $nombreDePoint = ($abonnement->getNbrePoint() != null && $abonnement->getNbrePoint() > 0) ?
        $abonnement->getNbrePoint() : $networkConfig->getNombreDePointsParInvitaton();
        $pourcentage = 100;
        // dd($personne);
        while ($personne->getParent() !== null && $cmp <= 5) {
            $parent = $personne->getParent();
            // dump("Parent");dd($parent);
            if ($parent->getUtilisateur()->getEleve() && $cmp > 1) {
                $pourcentage = $networkConfig->getPourcentageDistributionEleve();
            } elseif ($parent->getUtilisateur()->getEnseignant() && $cmp > 1) {
                $pourcentage = $networkConfig->getPourcentageDistributionEnseignant();
            }

            $nombreDePoint = ($nombreDePoint * $pourcentage) / 100;

            $newNombreDePoint = $parent->getUtilisateur()->getPoints() + $nombreDePoint;

            $parent->getUtilisateur()->setPoints($newNombreDePoint);

            $especes = $newNombreDePoint * $networkConfig->getTauxDeChange();
            $parent->getUtilisateur()->setEspeces($especes);

            $userRepository->save($parent->getUtilisateur());

            $personne = $personne->getParent();
            $cmp++;
        }

        $em->flush();

        return [
            'hasDone' => true,
            'message' => "Les points ont été distribués sans problèmes à tout le réseau",
        ];
    }

    /**
     * Cette methode permet de faire le retrait des points en xaf
     */
    public static function convertInMoney(User $user, float $montantARetirer, int $numeroTelephone, NetworkConfig $networkConfig, UserRepository $userRepository, Keys $keys, string $reference)
    {
        $points = $user->getPoints();
        $money = $user->getEspeces();

        if ($money < $networkConfig->getMinimumRetirable()) {
            return [
                'hasDone' => false,
                'message' => "Action impossible. Vous n'avez pas le minimum retirable.",
            ];
        }

        if ($money < $montantARetirer) {
            return [
                'hasDone' => false,
                'message' => "Vous ne pouvez pas retirer ce montant.",
            ];
        }

        $newPoints = $points - $montantARetirer / $networkConfig->getTauxDeChange();
        $user->setPoints($newPoints);
        $user->setEspeces($money - $montantARetirer);

        // On fait appel à l'API pour effectuer le retrait
        $requestData['transaction_amount'] = $montantARetirer;
        $requestData['transaction_currency'] = 'XAF';
        $requestData['transaction_reason'] = 'Retrait de fonds';
        $requestData['app_transaction_ref'] = $reference;
        $requestData['customer_phone_number'] = $numeroTelephone; //client
        $requestData['customer_name'] = $user->getPersonne()->getNomComplet();
        $requestData['customer_email'] = $user->getEmail();
        $requestData['customer_lang'] = 'fr';
        $requestData['transaction_receiver'] = $numeroTelephone; //client

        $response = MobileApiService::sendPayout(
            $requestData,
            $keys->getApiUrl(),
            $keys->getPrivateKey(),
            $keys->getCacert()
        );

        if (!$response['error']) {
            $userRepository->save($user, true);
        } else {
            return [
                'hasDone' => false,
                'message' => "Une erreur est survenue. Impossible de poursuivre l'opération. Reessayz plus tard SVP !",
                'response' => $response,
                'responseData' => null,
            ];
        }

        return [
            'hasDone' => true,
            'message' => "Votre retrait a été approuvé et confirmé.",
            'responseData' => $response['responseData'],
        ];
    }
}
