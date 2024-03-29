<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('color', ColorType::class)
            ->add('fullDayPrice', MoneyType::class, ['divisor' => 100, 'label' => 'Tagespreis'])
            ->add('halfDayPrice', MoneyType::class, ['divisor' => 100, 'label' => '4h-Preis'])
            ->add('fullDayPriceIntern', MoneyType::class, ['divisor' => 100, 'label' => 'Tagespreis intern'])
            ->add('halfDayPriceIntern', MoneyType::class, ['divisor' => 100, 'label' => '4h-Preis intern'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
