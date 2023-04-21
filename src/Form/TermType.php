<?php

namespace App\Form;

use App\Entity\Term;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('content', CKEditorType::class, [
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('isOnline', CheckboxType::class, [
                'required' => false,
                'label' => 'Put online'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Term::class,
            'attr' => ['class' => 'row g-4']
        ]);
    }
}
