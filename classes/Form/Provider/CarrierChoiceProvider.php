<?php
declare(strict_types=1);

namespace PrestaShop\Module\AaEndpointForSc\Form\Provider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CarrierChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    public function getChoices(array $options): array
    {
        $choices = [];
        $carriers = array_filter(\Carrier::getCarriers(\Context::getContext()->language->id, false, false, false, null, \Carrier::ALL_CARRIERS), function ($carrier) {
            return !$carrier['is_free'];
        });

        foreach ($carriers as $carrier) {
            $choices[$carrier['name']] = (int) $carrier['id_reference'];
        }

        return $choices;
    }
}
