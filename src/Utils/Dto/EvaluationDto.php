<?php 
namespace App\Utils\Dto;

use App\Entity\Evaluation;

class EvaluationDto {

    public static function from(Evaluation $evaluation): array
    {
        return [
            'titre' => $evaluation->getTitre(),
            'id' => $evaluation->getId(),
            'description' => $evaluation->getDescription(),
            'slug' => $evaluation->getSlug(),
            'startAt' => $evaluation->getStartAt(),
            'endAt' => $evaluation->getEndAt(),
            'duree' => $evaluation->getDuree(),
            'questionnaire' => $evaluation->getEvaluationQuestions()
        ];
    }
}