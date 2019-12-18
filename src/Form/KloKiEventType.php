<?php

namespace App\Form;

use App\Entity\Ausstattung;
use App\Entity\KloKiEvent;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KloKiEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $helper_query = function (UserRepository $repo) {
            return $repo->createQueryBuilder('u')
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%"ROLE_HELPER"%')
                ->orderBy('u.email', 'ASC');
        };

        $builder
            ->add('name')

            ->add('isFullDay', null, ['label' => 'Ganzer Tag'])
            ->add('startDate', DateType::class, ['html5' => false, 'widget' => 'single_text', 'label' => 'Von'])
            ->add('endDate',   DateType::class, ['html5' => false, 'widget' => 'single_text', 'label' => 'Bis'])
            ->add('startTime', TimeType::class, ['html5' => false, 'widget' => 'single_text', 'label' => false])
            ->add('endTime',   TimeType::class, ['html5' => false, 'widget' => 'single_text', 'label' => false])

            ->add('anzahlArtists', null, ['label' => 'Anzahl der Künstler'])
            ->add('isBestBenoetigt', null, ['label' => "Bestuhlung erforderlich"])
            ->add('isLichtBenoetigt', null, ['label' => "Licht erforderlich"])
            ->add('isTonBenoetigt', null, ['label' => "Ton erforderlich"])
            ->add('helperRequired', null, ['label' => "Helfer werden benötigt"])
            ->add('helperEinlassEins', EntityType::class, [
                    'class' => User::class,
                    'required' => false,
                    'query_builder' => $helper_query,
                    'label' => "Einlass 1"
            ])
            ->add('helperEinlassZwei', EntityType::class, [
                    'class' => User::class,
                    'required' => false,
                    'query_builder' => $helper_query,
                    'label' => "Einlass 2"
                ])
            ->add('helperKasse', EntityType::class, [
                    'class' => User::class,
                    'required' => false,
                    'query_builder' => $helper_query,
                    'label' => "Kasse"
                ])
            ->add('helperSpringerEins', EntityType::class, [
                    'class' => User::class,
                    'required' => false,
                    'query_builder' => $helper_query,
                    'label' => "Springer 1"
                ])
            ->add('helperSpringerZwei', EntityType::class, [
                    'class' => User::class,
                    'required' => false,
                    'query_builder' => $helper_query,
                    'label' => "Springer 2"
                ])

            ->add('art', null, ['label' => "Art"])
            ->add('kategorie', null, ['label' => "Kategorie"])
            ->add('room', null, ['label' => 'Raum'])
            ->add('kontakt', AddressSelectType::class, ['required' => true])
            ->add('bestPlan', null, ['label' => 'Bestuhlung'])
            ->add('stageOrder', null, ['label' => 'Bühnenanw.'])
            ->add('ParentEvent', null, ['label' => 'Hauptevent'])
            ->add('ausstattung', EntityType::class, ['class' => 'App:Ausstattung', 'multiple' => true, 'expanded' => false, 'attr' => ['title' => 'Ausstattung auswählen']])
            ->add('bemerkung', TextareaType::class)
            ->add('isFixed', ChoiceType::class, ['label' => 'Vertragsstatus', 'choices' => ['option' => 0, 'fest' => 1]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => KloKiEvent::class,
        ]);
    }
}
