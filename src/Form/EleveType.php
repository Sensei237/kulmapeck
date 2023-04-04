<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Etablissement;
use App\Entity\Filiere;
use App\Entity\Specialite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('utilisateur', RegistrationFormType::class)
            ->add('etablissement', EntityType::class, [
                'required' => false,
                'class' => Etablissement::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1 js-choice', 
                ]
            ])
            ->add('filiere', EntityType::class, [
                'mapped' => false,
                'class' => Filiere::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1 js-choice',
                ]
            ])
            ->add('specialite', EntityType::class, [
                'mapped' => false,
                'class' => Specialite::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1 js-choice',
                ]
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1 js-choice',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
