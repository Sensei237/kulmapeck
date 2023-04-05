<?php

namespace App\Form;

use App\Entity\SiteSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebSiteSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('siteName', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('siteCopyrights', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('siteEmail', EmailType::class,[
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('siteDescription', TextareaType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('contactPhone', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('supportEmail', EmailType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('contactAddress', TextareaType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteSetting::class,
            'label' => false,
            'attr' => ['class' => 'row g-4 align-items-center']
        ]);
    }
}
