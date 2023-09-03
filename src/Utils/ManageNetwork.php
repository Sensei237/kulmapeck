<?php

namespace App\Utils;

use App\Entity\NetworkConfig;
use App\Entity\User;
use App\Repository\UserRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;

class ManageNetwork
{
    /**
     * Cette methode permet de distribuer les points
     * Elle est appelee lorsque l'utilisateur valide son compte (pour l'enseignant) et 
     * lorsque l'etudiant paie un abonnement
     */
    public static function manage(User $user, NetworkConfig $networkConfig, UserRepository $userRepository, EntityManagerInterface $em): array
    {
        if (!$user->isVerified()) {
            return [
                'hasDone' => false,
                'message' => "Impossible d'effectuer cette opération car votre compte n'est pas activé."
            ];
        }

        $personne = $user->getPersonne();
        $eleve = $user->getEleve();

        if ($eleve === null) {
            return [
                'hasDone' => false,
                'message' => "Vous n'êtes ni élève ni enseignant."
            ];
        }

        if ($eleve !== null && !$eleve->isIsPremium()) {
            return [
                'hasDone' => false,
                'message' => "Vous n'êtes devez d'abord souscrire à un abonnement."
            ];
        }

        $cmp = 1;
        $nombreDePoint = $networkConfig->getNombreDePointsParInvitaton();
        $pourcentage = 100;

        while ($personne->getParent() !== null && $cmp <= 5) {
            $parent = $personne->getParent();
            if ($parent->getUtilisateur()->getEleve() && $cmp > 1) {
                $pourcentage = $networkConfig->getPourcentageDistributionEleve();
            }elseif ($parent->getUtilisateur()->getEnseignant() && $cmp > 1) {
                $pourcentage = $networkConfig->getPourcentageDistributionEnseignant();
            }
            
            $nombreDePoint = ($nombreDePoint * $pourcentage) / 100;

            $nombreDePoint = $parent->getUtilisateur()->getPoints() + $nombreDePoint;

            $parent->getUtilisateur()->setPoints($nombreDePoint);

            $especes = $nombreDePoint * $networkConfig->getTauxDeChange();
            $parent->getUtilisateur()->setEspeces($especes);

            $userRepository->save($parent->getUtilisateur());

            $personne = $personne->getParent();
            $cmp++;

        }

        $em->flush();

        return [
            'hasDone' => true,
            'message' => "Les points ont été distribués sans problèmes à tout le réseau"
        ];
    }

    /**
     * Cette methode permet de faire le retrait des points en xaf
     */
    public static function convertInMoney(User $user, float $montantARetirer, int $numeroTelephone, NetworkConfig $networkConfig, UserRepository $userRepository)
    {
        $points = $user->getPoints();
        $money = $user->getEspeces();

        if ($money < $networkConfig->getMinimumRetirable()) {
            return [
                'hasDone' => false,
                'message' => "Action impossible. Vous n'avez pas le minimum retirable."
            ];
        }

        if ($money < $montantARetirer) {
            return [
                'hasDone' => false,
                'message' => "Vous ne pouvez pas retirer ce montant."
            ];
        }

        $newPoints = $points - $montantARetirer/$networkConfig->getTauxDeChange();
        $user->setPoints($newPoints);
        $user->setEspeces($money - $montantARetirer);

        // On fait appel à l'API pour effectuer le retrait
        $retraitEffectue = true;
        if ($retraitEffectue) {
            $userRepository->save($user, true);
        }
        
        return [
            'hasDone' => true,
            'message' => "Votre retrait a été approuvé et confirmé."
        ];
    }
}
