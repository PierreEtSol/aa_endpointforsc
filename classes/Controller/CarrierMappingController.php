<?php

namespace PrestaShop\Module\AaEndpointForSc\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\Module\AaEndpointForSc\Services\Carriers\SendCloudCarriers;
class CarrierMappingController extends FrameworkBundleAdminController
{
    public function viewAction(Request $request)
    {
        $sendCloudApi = $this->get('prestashop.module.aa_endpointforsc.sendcloud_carriers');
        $sendCloudCarriers =  $sendCloudApi->getSendCloudCarriers();

//        $repository = $this->get('prestashop.module.aa_endpointforsc.carrier_mapping.repository');
//        $repository->deleteSendCloudCarriers($sendCloudCarriers);
//        $repository->createSendCloudCarriers($sendCloudCarriers);
        //die;

        $form = $this->get('prestashop.module.aa_endpointforsc.carrier_mapping.form_handler')->getForm();

        return $this->render('@Modules/aa_endpointforsc/views/templates/admin/form.html.twig', [
            'crossSellingForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    public function processAction(Request $request)
    {

        /** @var FormHandlerInterface $formHandler */

        $formHandler = $this->get('prestashop.module.aa_endpointforsc.carrier_mapping.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveCarrierMapping($request);
            return $this->redirectToRoute('admin_carrier_mapping_form_view');
        }

        return $this->render('@Modules/aa_endpointforsc/views/templates/admin/form.html.twig', [
            'crossSellingForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);

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
    private function saveCarrierMapping($request): void
    {
        $repository = $this->get('prestashop.module.aa_endpointforsc.carrier_mapping.repository');
        $mapping = $repository->getCarrierMapping();

        foreach ( $mapping  as $key => $mappingRecord) {
            $repository->deleteMappingRecord($mappingRecord['id_sc_carrier'],  $mappingRecord['id_ps_reference_carrier']);
        }

        $requestMapping =  isset($request->request->all()['carrier_mapping']) ? $request->request->all()['carrier_mapping'] : [];

        foreach ($requestMapping  as $id_sc_carrier => $id_ps_reference_carrier) {
            if (empty($id_ps_reference_carrier)) $id_ps_reference_carrier = null;
            $repository->createMappingRecord($id_sc_carrier,  $id_ps_reference_carrier );

        }
    }
}
