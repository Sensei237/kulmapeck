<?php

namespace App\Form;

use App\Entity\Etablissement;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtablissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'School name',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1',
                    'placeholder' => 'Enter School name'
                ]
            ])
            ->add('ville', null, [
                'label' => 'Town',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1',
                    'placeholder' => 'Enter School city'
                ]
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'name',
                'label' => 'Country',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'placeholder' => 'Select Country',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1 js-choice',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etablissement::class,
        ]);
    }
}
