<?php

namespace PrestaShop\Module\AaEndpointForSc\Form\Type;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShopBundle\Form\Admin\Type\CustomContentType;

class CarrierMappingType extends TranslatorAwareType
{
    private $carrierChoiceProvider;
    private $repository;
    /**
     * CarrierFooterType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $repository,
        $carrierChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->repository = $repository;
        $this->carrierChoiceProvider = $carrierChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mapping = $this->repository->getCarrierMapping();

        foreach ($mapping as $key => $value) {
            if (!empty($value['id_ps_reference_carrier']))
            {
                $selectedCarrierName = array_search($value['id_ps_reference_carrier'], $this->carrierChoiceProvider->getChoices([]));
            }
            else $selectedCarrierName = '';
            $nameScCarrierFormatted = 'sc_carrier_' . $value['id_sc_carrier'];
            $builder->add($nameScCarrierFormatted, CustomContentType::class, [
                'label' => $value['name'],
                'template' => '@Modules/aa_endpointforsc/views/templates/admin/subform.html.twig',
                'data' => [
                    'mapping' => $value,
                    'choices' => $this->carrierChoiceProvider->getChoices([]),
                    'selected_carrier_name' => $selectedCarrierName
               ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'module_aa_carriersfooter';
    }
}
