<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\PaymentMethod;
use App\Entity\SkillLevel;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Cours1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Course category',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'placeholder' => 'Select course category',
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'label' => 'Classes',
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('intitule', TextType::class, [
                'label' => 'Course title',
                'required' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter course title'
                ],
            ])
            ->add('niveauDifficulte', ChoiceType::class, [
                'choices' => [
                    'Facile' => 'Facile',
                    'Intermédiaire' => 'Intermédiaire',
                    'Difficile' => 'Difficile',
                    'Très difficile' => 'Très difficile',
                    'Tout Niveau' => 'Tout Niveau'
                ],
                'label' => 'Course level',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'placeholder' => 'Select course level',
            ])
            ->add('dureeApprentissage', TextType::class, [
                'label' => "Course time",
                'attr' => [
                    'placeholder' => "Exemple: 10 heures"
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Course content',
                'required' => false,
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Short description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Enter keywords",
                    'rows' => "2"
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('isFree', CheckboxType::class, [
                'label' => 'Check if this course is free',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('montantAbonnement', IntegerType::class, [
                'required' => false,
                'label' => 'Course price',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter course price'
                ],
            ])
            ->add('numberOfLessons', IntegerType::class, [
                'required' => false,
                'label' => 'Course lessons',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the number of lessons'
                ],
            ])
            ->add('media', MediaType::class)
            ->add('chapitres', CollectionType::class, [
                'entry_type' => Chapitre1Type::class,
                'entry_options' => ['label'=>false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ])
            ->add('fAQs', CollectionType::class, [
                'entry_type' => FAQType::class,
                'entry_options' => ['label'=>false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ])
            ->add('language', ChoiceType::class, [
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
            ->add('tags', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control js-choice mb-0"',
                    'data-placeholder' =>"true",
                    'data-placeholder-Val' => "Enter tags", 
                    'data-max-item-count' => "14", 
                    'data-remove-item-button' => "true"
                ],
                'required' => false,
            ])
            ->add('paymentMethods', EntityType::class, [
                'class' => PaymentMethod::class,
                'choice_label' => 'label',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'label' => 'Payments methods',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
            ])
            ->add('skillLevel', EntityType::class, [
                'class' => SkillLevel::class,
                'choice_label' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'aria-label' => ".form-select-sm",
                    'data-search-enabled' => "true"
                ],
                'label' => 'Skill Level',
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
