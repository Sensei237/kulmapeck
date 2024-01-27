<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Personne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'last_name'
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1',
                    'id' => 'first_name'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => "Nom d'utilisateurs (Il faut 5 caractères minimum et 8 maximum)",
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'pseudo',
                    'label' => "Nom d'utilisateurs (Il faut 5 caractères minimum et 8 maximum)"
                ]
            ])
            ->add('bornAt', BirthdayType::class, [
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1',
                    'id' => 'pdate_naissance'
                ],
                'widget' => 'single_text'
            ])
            ->add('lieuNaissance', TextType::class, [
                'label' => 'Lieu de naissance',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'lieu_naissance'
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'Masculin',
                    'Feminin' => 'Feminin'
                ],
                'label' => 'Genre',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1', 
                    'id' => 'sexe'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Sélectionner une image',
                    'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG'
                ],
                'required' => false,
                'label' => 'Photo de profil',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'photo_profil'
                ]
            ])
            ->add('adresse', TextType::class, [
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'adresse'
                ],
                'label' => 'Adresse',
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'last_name'
                ],
                'label' => 'Numéro de téléphone'
            ])
            // ->add('invitationCode')
            // ->add('invitationLink')
            // ->add('utilisateur')
            // ->add('parent')
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1', 
                    'id' => 'pays'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
