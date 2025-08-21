<?php

namespace PrestaShop\Module\AaEndpointForSc\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

class CarrierMappingController extends FrameworkBundleAdminController
{
    public function viewAction(Request $request)
    {
        //$this->get('prestashop.module.aa_carriersfooter.form_provider')->setIdcarrierFooter($carrierFooterId);
        $form = $this->get('prestashop.module.aa_crossselling.global.form_handler')->getForm();

        return $this->render('@Modules/aa_crossselling/views/templates/admin/form.html.twig', [
            'crossSellingForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    public function processAction(Request $request)
    {

        /** @var FormHandlerInterface $formHandler */
        /*
        $formHandler = $this->get('prestashop.module.aa_crossselling.global.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveGlobalCrossSelling($request);
            return $this->redirectToRoute('admin_crossselling_global_view');
        }

        return $this->render('@Modules/aa_crossselling/views/templates/admin/form.html.twig', [
            'crossSellingForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
        */
    }

    /**
     * Gets the header toolbar buttons.
     *
     * @return array
     */
    private function getToolbarButtons()
    {
        return [
        ];
    }


    /**
     * @param array $params
     */
    private function saveGlobalCrossSelling($request): void
    {
        $repository = $this->get('prestashop.module.aa_crossselling.repository');
        $products = $repository->getGlobalCrossSellingProducts();

        foreach ($products as $product) {
            $repository->deleteGlobalCrossSellingProduct($product['id_product']);
        }
        $requestProducts =  isset($request->request->all()['global']['crossselling']) ? $request->request->all()['global']['crossselling'] : [];
        foreach ($requestProducts  as $key => $product) {
            $repository->createForGlobal($product['id_product']);

        }
    }
}
