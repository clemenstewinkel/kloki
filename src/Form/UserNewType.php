<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => [
                        'ROLE_ADMIN'    => 'ROLE_ADMIN',
                        'ROLE_FOOD'     => 'ROLE_FOOD',
                        'ROLE_TECH'     => 'ROLE_TECH',
                        'ROLE_HELPER'   => 'ROLE_HELPER',
                        'ROLE_LANDLORD' => 'ROLE_LANDLORD'
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label'    => 'Berechtigungen'
                ]
            )
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
