<?php

namespace App\Form;

use App\Entity\Sujet;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SujetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'one form-control pe-4 bg-light',
                    'rows' => '1',
                    'placeholder' => 'Ask your question',
                    'style' => 'height: 36px;'
                ],
                'label' => false,
            ])
            // ->add('isSolved')
            // ->add('createdAt')
            // ->add('membre')
            // ->add('forum')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sujet::class,
            'attr' => [
                'class' => 'w-100 d-flex'
            ]
        ]);
    }
}
