<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Classe;
use App\Entity\Exam;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Title ( Ex: Examen de chimie session 2023 serie C et D)", 
                'label_attr' => [
                    'class' => 'form-label'
                ],
            'attr' => ['placeholder' => 'example: Examen de chimie session 2023 serie C et D']

            ])
            ->add('duration', TextType::class, [
                'label' => "Duration",
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => ['placeholder' => 'example: 02 heures']
            ])
            ->add('description', TextareaType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('sujetFile', FileType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('correctionFile', FileType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
                
            ])
            ->add('classe', EntityType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'class' => Classe::class,
                'choice_label' => 'name',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'choices' => [
                    'English' => 'English',
                    'French' => 'French',
                ],
                'placeholder' => 'Select course language',
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
            ])
            ->add('category', EntityType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
            ])
            ->add('image', FileType::class, [
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
            'label' => false,
        ]);
    }
}
