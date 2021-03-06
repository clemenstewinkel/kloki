<?php

namespace App\Form;

use App\Entity\Bestuhlungsplan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BestuhlungsplanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sitzplaetze', null, ['label' => 'Sitzplätze unten'])
            ->add('sitzplaetzeOben', null, ['label' => 'Sitzplätze Empore'])
            ->add('stehplaetze', null, ['label' => 'Stehplätze'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bestuhlungsplan::class,
        ]);
    }
}


