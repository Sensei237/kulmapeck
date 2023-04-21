<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Your name *',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control-lg'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email address *',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control-lg'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message *',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['rows' => '4'],
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
