<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\AbonnementItem;
use App\Entity\PaymentMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('montant')
            ->add('NbrePoint')
            ->add('duree')
            ->add('isRecommended')
            ->add('items', EntityType::class, [
                'class' => AbonnementItem::class,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true,
            ])
            -> add('paymentMethods', EntityType::class, [
                'class' => PaymentMethod::class,
                'choice_label' => 'label',
                'multiple' => true,
                'attr' => [
                    'class' => 'js-choice'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
