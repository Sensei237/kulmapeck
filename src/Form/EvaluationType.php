<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Classe;
use App\Entity\Evaluation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EvaluationType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('startAt', DateTimeType::class, [
                    'widget' => 'single_text',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                ])
                ->add('endAt', DateTimeType::class, [
                    'widget' => 'single_text',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                ]);
        }
        $builder
            ->add('titre', TextType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('duree', NumberType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'label' => 'Durée'
            ])
            ->add('isGeneratedRandomQuestions', CheckboxType::class, [
                'required' => false,
                'label' => 'Questionnaire aléatoire pour chaque élève'
            ])
            ->add('matiere', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'label' => 'Matière'
            ])
            ->add('classes', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
