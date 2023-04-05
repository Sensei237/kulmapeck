<?php

namespace App\Form;

use App\Entity\SiteSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mainSiteUrl', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('isMaintenanceMode', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input mt-0 price-toggle me-2'],
                'label_attr' => ['class' => 'form-check-label mt-1'],
                'label' => 'Make Site Offline',
                'required' => false,
            ])
            ->add('maintenanceText', TextareaType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteSetting::class,
            'label' => false,
            'attr' => ['class' => 'row g-4']
        ]);
    }
}
