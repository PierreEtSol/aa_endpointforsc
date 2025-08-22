<?php

namespace PrestaShop\Module\AaEndpointForSc\Form\Type;

use PrestaShopBundle\Form\Admin\Type\CustomContentType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Context;
use Product;

class CarrierMappingType extends TranslatorAwareType
{
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
        $repository
    ) {
        parent::__construct($translator, $locales);
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $this->getTranslator();
        $mapping = $this->repository->getCarrierMapping();

        $builder
            ->add('crossselling_products', CustomContentType::class, [
                'label' => $translator->trans('Products', [],'Modules.AaCrossselling.Admin'),
                'required' => false,
                'attr' => ['class' => 'col-md-12'],
                'template' => '@Modules/aa_endpointforsc/views/templates/admin/subform.html.twig',
                'data' => [
                    'mapping' => $mapping
                ],
            ]);
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
