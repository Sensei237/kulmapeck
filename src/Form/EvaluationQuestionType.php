<?php

namespace App\Form;

use App\Entity\EvaluationQuestion;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', CKEditorType::class, [
                'label' => 'Question',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('proposition1', CKEditorType::class, [
                'label' => 'Proposition 1',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('proposition2', CKEditorType::class, [
                'label' => 'Proposition 2',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('proposition3', CKEditorType::class, [
                'label' => 'Proposition 3',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('proposition4', CKEditorType::class, [
                'label' => 'Proposition 4',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('propositionJuste', ChoiceType::class, [
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1', 
                    'id' => 'sexe'
                ],
                'label_attr' => ['class' => 'form-label'],
                'choices' => [
                    'Propoition 1' => 1,
                    'Propoition 2' => 2,
                    'Propoition 3' => 3,
                    'Propoition 4' => 4,
                ],
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EvaluationQuestion::class,
        ]);
    }
}
