<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\SousSysteme;
use App\Entity\TypeEnseignement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiliereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Filiere Name',
                'required' => true,
                'attr' => [
                    'class' => 'Enter filiere name'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('sousSystemes', EntityType::class, [
                'class' => SousSysteme::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Sous système',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('typeEnseignement', EntityType::class, [
                'class' => TypeEnseignement::class,
                'choice_label' => 'name',
                'label' => "Type d'enseignement",
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filiere::class,
        ]);
    }
}
