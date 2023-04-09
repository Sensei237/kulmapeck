<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\SkillLevel;
use App\Entity\SousSysteme;
use App\Entity\Specialite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom de la classe',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('skillLevel', EntityType::class, [
                'class' => SkillLevel::class,
                'choice_label' => 'name',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
                'label' => 'Level'
            ])
            ->add('specialite', EntityType::class, [
                'class' => Specialite::class,
                'choice_label' => 'name',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
                'required' => false,
            ])
            ->add('sousSysteme', EntityType::class, [
                'class' => SousSysteme::class,
                'choice_label' => 'name',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
