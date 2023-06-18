<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Formation;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Intitulé de la formation',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image à la une',
                'attr' => [
                    'placeholder' => 'Sélectionner une image',
                    'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
                ],
                'required' => false,
                'label_attr' => ['class' => 'form-label']
            ])
            // ->add('slug')
            ->add('description', CKEditorType::class)
            ->add('duree', null, [
                'label' => "Durée d'apprentissage",
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'placeholder' => 'Exemple: 03 mois',
                    
                ]
            ])
            ->add('niveauDifficulte', ChoiceType::class, [
                'choices' => [
                    'Facile' => 'Facile',
                    'Intermédiaire' => 'Intermédiaire',
                    'Difficile' => 'Difficile',
                    'Très difficile' => 'Très difficile'
                ],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Niveau de difficulté',
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'intitule',
                'required' => true,
                'multiple' => true,
                'label' => "Liste de cours associés",
                'attr' => [
                    'class' => 'form-select js-choice border-0 z-index-9 bg-transparent',
                    'data-placeholder' =>"true",
                    'data-placeholder-Val' => "Ajouter un cours", 
                    'data-max-item-count' => "20", 
                    'data-remove-item-button' => "true"
                ],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('isFree', CheckboxType::class, [
                'label' => "Cette formation est gratuite",
                'required' => false,
            ])
            // ->add('createdAt')
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publier',
                'required' => false,
            ])
            
            // ->add('eleves')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
            'attr' => [
                'class' => 'row g-3'
            ]
        ]);
    }
}
