<?php

namespace App\Form;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write a question'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('proposition1', TextType::class, [
                'label' => 'Proposition 1',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write a option'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('proposition2', TextType::class, [
                'label' => 'Proposition 2',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write a option'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('proposition3', TextType::class, [
                'label' => 'Proposition 3',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write a option'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('proposition4', TextType::class, [
                'label' => 'Proposition 4',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write a option'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('propositionJuste', ChoiceType::class, [
                'choices' => [
                    'Proposition 1' => 1,
                    'Proposition 2' => 2,
                    'Proposition 3' => 3,
                    'Proposition 4' => 4
                ],
                'attr' => [
                    'class' => 'form-control js-choice'
                ],
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
