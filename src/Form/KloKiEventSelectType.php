<?php


namespace App\Form;


use App\Form\DataTransformer\EventFieldTransformer;
use App\Repository\KloKiEventRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class KloKiEventSelectType extends AbstractType
{
    /**
     * @var KloKiEventRepository
     */
    private $eventRepo;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(KloKiEventRepository $eventRepo, RouterInterface $router)
    {
        $this->eventRepo = $eventRepo;
        $this->router = $router;
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EventFieldTransformer($this->eventRepo));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Event nicht gefunden!',
            'attr' => [
                'class' => 'js-event-autocomplete',
                'data-autocomplete-url' => $this->router->generate('event_auto_complete')
            ]
        ]);
    }

}