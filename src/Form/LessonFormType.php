<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            -> add('title', TextType::class, [
                'label' => 'Lesson title', 
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter lesson title'
                ]
            ])
            
            ->add('numero', NumberType::class, [
                'label' => 'Lesson number', 
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter lesson Number'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Lesson content',
                'label_attr' => ['class' => 'form-label'],
            ])
            
            ->add('videoLink', UrlType::class, [
                'label' => 'Video url', 
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control', 
                    'placeholder' => 'Enter lesson video link'
                ],
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
