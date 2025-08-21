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
        $products = $this->repository->getGlobalCrossSellingProducts();
        foreach($products as &$product) {
            $productTmp = new Product($product['id_product'], false, Context::getContext()->language->id);
            $img = $productTmp->getCover($productTmp->id);
            $image_type = 'small_default'; //Mirar el ftp para ver otros tipos
            $imagen = Context::getContext()->link->getImageLink(isset($productTmp->link_rewrite) ? $productTmp->link_rewrite : $productTmp->name, (int)$img['id_image'], $image_type);
            $product['image'] = $imagen;
        }
        $builder
            ->add('crossselling_products', CustomContentType::class, [
                'label' => $translator->trans('Products', [],'Modules.AaCrossselling.Admin'),
                'required' => false,
                'attr' => ['class' => 'col-md-12'],
                'template' => '@Modules/aa_crossselling/views/templates/admin/crossselling_subform.html.twig',
                'data' => [
                    'products' => $products,
                    'type' => 'global'
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
