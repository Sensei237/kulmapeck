<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Enseignant;
use App\Entity\Etablissement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('reference')
            // ->add('utilisateur')
            ->add('etablissement', EntityType::class, [
                'required' => false,
                'class' => Etablissement::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1',
                ],
            ])
            ->add('discipline', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1',
                ]
            ])
            // ->add('diplomeFile', FileType::class, [
            //     'mapped' => true,
            //     'attr' => [
            //         'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
            //         'class' => 'border-0 bg-light rounded-end ps-1', 
            //     ],
            //     'required' => true,
            //     'label' => 'Copie du dernier diplôme <sup class="text-danger">*</sup>',
            //     'label_html' => true,
            // ])
            ->add('rectoCNIFile', FileType::class, [
                'mapped' => true,
                'attr' => [
                    'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                ],
                'required' => true,
                'label' => 'Recto de votre CNI <sup class="text-danger">*</sup>',
                'label_html' => true,
            ])
            ->add('versoCNIFile', FileType::class, [
                'mapped' => true,
                'attr' => [
                    'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                ],
                'required' => true,
                'label' => 'Verso de votre CNI <sup class="text-danger">*</sup>',
                'label_html' => true,
            ])
            ->add('selfieCNIFile', FileType::class, [
                'mapped' => true,
                'attr' => [
                    'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                ],
                'required' => true,
                'label' => 'Selfie de vous tenant votre CNI <sup class="text-danger">*</sup>',
                'label_html' => true,
            ])
            // ->add('emploiDuTempsFile', FileType::class, [
            //     'mapped' => true,
            //     'attr' => [
            //         'accept' => '.png, .jpg, .PNG, .JPG, .jpeg, .JPEG',
            //         'class' => 'border-0 bg-light rounded-end ps-1', 
            //     ],
            //     'required' => true,
            //     'label' => "Copie de l'emploi du temps de l'année précédente <sup class='text-danger'>*</sup>",
            //     'label_html' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
