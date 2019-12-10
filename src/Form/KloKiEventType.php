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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('beginAt', DateTimeType::class, ['html5'=> false, 'widget' => 'single_text', 'label' => "Beginn", 'attr' => ['autocomplete' => 'off']])
            ->add('endAt', DateTimeType::class, ['html5'=> false, 'widget' => 'single_text', 'label' => "Ende", 'attr' => ['autocomplete' => 'off']])
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
            ->add('kontakt', null, ['required' => true])
            ->add('bestPlan', null, ['label' => 'Auswahl des Bestuhlungsplanes'])
            ->add('stageOrder', null, ['label' => 'Auswahl der Bühnenanweisung'])
            ->add('ParentEvent', null, ['label' => 'Hauptevent'])
            ->add('ausstattung', EntityType::class, ['class' => 'App:Ausstattung', 'multiple' => true, 'expanded' => true])
            ->add('bemerkung', TextareaType::class)
            ->add('isFixed', ChoiceType::class, ['label' => 'Vertragsstatus', 'choices' => ['optioniert' => 0, 'fest' => 1]])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if($form->has('takeNewAddress'))
                {
                    if (isset($data['takeNewAddress'])) {
                        $form
                            //->remove('kontakt')
                            ->add('kontakt', null, ['required' => true])
                            ->add('kontaktNeu', AddresseType::class, [
                                    'required' => true,
                                    'mapped' => true,
                                    'property_path' => 'kontakt'
                                ]
                            )
                        ;
                    }
                    else {
                        $form
                            ->add('kontakt', null, ['required' => true, 'mapped' => true])
                            ->add('kontaktNeu', AddresseType::class, [
                                    'required' => false,
                                    'mapped' => false,
                                    'property_path' => 'kontakt'
                                ]
                            )
                        ;
                    }
                }
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => KloKiEvent::class,
        ]);
    }
}
