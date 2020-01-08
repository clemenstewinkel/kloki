<?php

namespace App\Form;

use App\Entity\KloKiEvent;
use App\Entity\User;
use App\Repository\KloKiEventRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KloKiEventFoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event_query = function (KloKiEventRepository $repo) {
            return $repo->createQueryBuilder('event')
                ->orderBy('event.start', 'ASC');
        };

        $builder
            ->add('name')
            ->add('allDay', null, ['label' => 'Ganzer Tag'])
            ->add('startDate', DateType::class, ['html5' => false, 'widget' => 'single_text', 'label' => 'Von'])
            ->add('endDate',   DateType::class, ['html5' => false, 'widget' => 'single_text', 'label' => 'Bis'])
            ->add('startTime', TimeType::class, ['html5' => false, 'widget' => 'single_text', 'label' => false])
            ->add('endTime',   TimeType::class, ['html5' => false, 'widget' => 'single_text', 'label' => false])
            ->add('isBestBenoetigt',    null, ['label' => "Bestuhlung erforderlich"])
            ->add('isLichtBenoetigt',   null, ['label' => "Licht erforderlich"])
            ->add('isTonBenoetigt',     null, ['label' => "Ton erforderlich"])
            ->add('pleaseMakeContract', CheckboxType::class)
            ->add('helperRequired', null, ['label' => "Helfer werden benötigt"])
            ->add('kategorie', null, ['label' => "Kategorie"])
            ->add('room', null, ['label' => 'Raum'])
            ->add('kontakt', AddressSelectType::class, ['required' => true])
            ->add('bestPlan', null, ['label' => 'Bestuhlung'])
            ->add('stageOrder', null, ['label' => 'Bühnenanw.'])
            ->add('ParentEvent', EntityType::class, [
                'class' => KloKiEvent::class,
                'required' => false,
                'query_builder' => $event_query,
                'label' => "Hauptevent"
            ])


            ->add('ausstattung', EntityType::class, ['class' => 'App:Ausstattung', 'multiple' => true, 'expanded' => false, 'attr' => ['title' => 'Ausstattung auswählen']])
            ->add('bemerkung', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => KloKiEvent::class,
        ]);
    }
}
