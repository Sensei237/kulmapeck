<?php

namespace App\Form;

use App\Entity\NetworkConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetworkConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombreDePointsParInvitaton', NumberType::class)
            ->add('pourcentageDistributionEnseignant', NumberType::class)
            ->add('pourcentageDistributionEleve', NumberType::class)
            ->add('tauxDeChange', NumberType::class)
            ->add('minimumRetirable', NumberType::class, [
                'label' => "Minimun retirable (en XAF)"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NetworkConfig::class,
            'attr' => [
                'class' => 'row g-4'
            ]
        ]);
    }
}
