<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => [
                    '★★★★★ (5/5)' => '5',
                    '★★★★☆ (4/5)' => '4',
                    '★★★☆☆ (3/5)' => '3',
                    '★★☆☆☆ (2/5)' => '2',
                    '★☆☆☆☆ (1/5)' => '1',
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-select js-choice',
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'row' => '3',
                    'placeholder' => 'Your review'
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
