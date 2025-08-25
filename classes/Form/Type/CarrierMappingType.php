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
        $translator = $this->getTranslator();
        $mapping = $this->repository->getCarrierMapping();
        //echo "<pre>";
        //var_dump($mapping);die;
        foreach ($mapping as $key => $value) {
            //echo "<pre>";
            var_dump($key);
            $selectedCarrierName = array_search($value['id_ps_reference_carrier'], $this->carrierChoiceProvider->getChoices([]));
            //var_dump($selectedCarrierName);//die;
            // $nameScCarrierFormatted is the concatenation of id_ps_reference_carrier and sc carrier code.
            // Slashe and equal charcaters in Sc Code are replaced by underscode to fit Sf Requirements
            //$nameScCarrierFormatted =  str_replace(["/", "=", ":", '.'], "_", $value['code']) . '_' . $value['id_ps_reference_carrier'];
            $nameScCarrierFormatted = 'sc_carrier_' . $value['id_sc_carrier'];
//            $builder
//                ->add( $nameScCarrierFormatted, ChoiceType::class, [
//                    'choices' => $this->carrierChoiceProvider->getChoices([]),
//                    'label' => $value['code'],
//                    'required' => false,
//                    //'attr' => ['class' => 'col-md-12'],
//                    'data' => $selectedCarrierName
//                    //'template' => '@Modules/aa_endpointforsc/views/templates/admin/subform.html.twig',
////                    'data' => [
////                        'mapping' => $mapping
////                    ],
//                ]);

            $builder->add($nameScCarrierFormatted, CustomContentType::class, [
                'label' => $value['code'],
                //'required' => false,
                //'attr' => ['class' => 'col-md-12'],
                'template' => '@Modules/aa_endpointforsc/views/templates/admin/subform.html.twig',
                'data' => [
                    'mapping' => $value,
                    'choices' => $this->carrierChoiceProvider->getChoices([]),
                    'selected_carrier_name' => $selectedCarrierName
               ],
            ]);
        }
        //var_dump($builder-);//die;
        //die;
//
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
