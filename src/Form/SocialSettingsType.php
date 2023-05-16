<?php

namespace App\Form;

use App\Entity\SocialSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('link', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('icon', ChoiceType::class, [
                'choices' => [
                    'Facebook Icon' => 'fa-facebook-f',
                    'Instagram Icon' => 'fa-instagram',
                    'Twitter Icon' => 'fa-twitter',
                    'LinkedIn Icon' => 'fa-linkedin-in',
                    'Youtube Icon' => 'fa-youtube'
                ],
                'attr' => ['class' => 'js-choice'],
                'label_attr' => ['class' => 'form-label']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialSetting::class,
        ]);
    }
}
