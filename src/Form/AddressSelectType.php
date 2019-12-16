<?php


namespace App\Form;


use App\Form\DataTransformer\AddressFieldTransformer;
use App\Repository\AddresseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AddressSelectType extends AbstractType
{
    /**
     * @var AddresseRepository
     */
    private $addressRepo;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(AddresseRepository $addressRepo, RouterInterface $router)
    {
        $this->addressRepo = $addressRepo;
        $this->router = $router;
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new AddressFieldTransformer($this->addressRepo));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Adresse nicht gefunden!',
            'attr' => [
                'class' => 'js-address-autocomplete',
                'data-autocomplete-url' => $this->router->generate('addresse_auto_complete')
            ]
        ]);
    }

}