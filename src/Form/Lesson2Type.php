<?php

namespace App\Form;

use App\Entity\Lesson;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Lesson2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Lesson title', 
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('numero', NumberType::class)
            ->add('content', CKEditorType::class)
            ->add('videoLink')
            ->add('videoFile', FileType::class, [
                'required' => false,
                'label' => 'Video File',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'accept' => '.mp4'
                ]
            ])
            ->add('posterFile', FileType::class, [
                'required' => false,
                'label' => 'Poster',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'accept' => '.png, .jpg, .jpeg'
                ]
            ])
            ->add('isPremium', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
