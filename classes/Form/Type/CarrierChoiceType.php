<?php
declare(strict_types=1);

namespace PrestaShop\Module\AaEndpointForSc\Form\Type;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CarrierChoiceType extends AbstractType
{
    public function __construct(private ConfigurableFormChoiceProviderInterface $carrierChoiceProvider)
    {
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setNormalizer(
            'choices',
            function (Options $options) {

                return $this->carrierChoiceProvider->getChoices((array) $options);
            }
        );
    }
}
