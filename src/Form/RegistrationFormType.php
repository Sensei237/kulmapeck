<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'last_name'
                ],
                'label' => 'Email adress'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label_html' => true,
                'label' => "By signing up, you agree to the <a href=''>terms</a>"
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 50,
                    ]),
                ],
                'label' => 'Password',
                'attr' => [
                    'class' => 'border-0 bg-light rounded-end ps-1', 
                    'id' => 'last_name',
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add("personne", PersonneType::class)
            ->add("filieres", EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => "Can check courses in",
                'attr' => [
                    'class' => 'border-0 js-choice bg-light rounded-end ps-1',
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Can manage Courses and formations' => 'ROLE_COURSE_MANAGER',
                    'Can manage Students' => 'ROLE_STUDENTS_MANAGER',
                    'Can manage Trainers' => 'ROLE_TRAINERS_MANAGER',
                    'Super Adminitrator' => 'ROLE_SUPER_USER'
                ],
                'attr' => [
                    'class' => 'js-choice'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
