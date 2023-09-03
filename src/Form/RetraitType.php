<?php

namespace App\Form;

use App\Entity\PaymentMethod;
use App\Entity\Retrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetraitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('numeroTelephone')
            ->add('paymentMethod', EntityType::class, [
                'class' => PaymentMethod::class,
                'choice_label' => 'label',
                'required' => true,
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Retrait::class,
            'attr' => [
                'class' => 'row g-2'
            ]
        ]);
    }
}
