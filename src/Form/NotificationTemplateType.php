<?php

namespace App\Form;

use App\Entity\NotificationTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('template')
            ->add('notificationType', ChoiceType::class, [
                'choices' => [
                    'When Course Published By a Teacher' => 1,
                    'When an administrator has validated a course (Approve course)' => 2,
                    'When an administrator has rejeted a course' => 3,
                    'When a student become premium (Subscribe a plan)' => 4,
                    'When a student subscribe to a course' => 5,
                    'Other' => 0,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotificationTemplate::class,
        ]);
    }
}
