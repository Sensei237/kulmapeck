<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Personne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PersonneFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', null, [
                'label' => $this->translator->trans('PERSONNE_NAME_KEY')
            ])
            ->add('firstName')
            ->add('pseudo')
            ->add('bornAt', BirthdayType::class, [
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1',
                    'id' => 'pdate_naissance'
                ],
                'widget' => 'single_text'
            ])
            ->add('lieuNaissance')
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'Masculin',
                    'Feminin' => 'Feminin'
                ],
                'label' => 'Genre',
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1', 
                    'id' => 'sexe'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
            ])
            ->add('adresse')
            ->add('telephone')
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
