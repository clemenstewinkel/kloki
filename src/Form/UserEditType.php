<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => [
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                        'ROLE_FOOD'     => 'ROLE_FOOD',
                        'ROLE_TECH'     => 'ROLE_TECH',
                        'ROLE_HELPER'   => 'ROLE_HELPER',
                        'ROLE_LANDLORD' => 'ROLE_LANDLORD',
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'label'    => 'Berechtigungen'
                ]
            )
            ->add('plainPassword', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
