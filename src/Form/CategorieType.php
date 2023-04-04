<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter category name',
                    'class' => 'form-control'
                ],
                'label' => 'Category Name',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => true,
                'label' => 'Category image',
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/gif, image/jpeg, image/png'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'label' => false,
        ]);
    }
}
