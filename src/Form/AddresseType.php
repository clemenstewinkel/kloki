<?php

namespace App\Form;

use App\Entity\Addresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vorname')
            ->add('nachname', null, ['required' => true])
            ->add('strasse')
            ->add('plz')
            ->add('ort')
            ->add('telefon', null, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Addresse::class,
        ]);
    }
}
