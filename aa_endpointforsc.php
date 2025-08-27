<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PrestaShop\Module\AaEndpointForSc\Repository\CarrierMappingRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
#use PrestaShop\Module\AaCarriersFooter\Model\CarrierFooter;

class Aa_Endpointforsc extends Module
{
    private $repository;

    public function __construct()
    {
        $this->name = 'aa_endpointforsc';
        $this->tab = 'shipping_logistics';
        $this->author = 'Adib Aroui';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];

        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('AA SendCloud WebHook Endpoint', [], 'Modules.Endpointsc.Admin');
        $this->description = $this->trans('This module creates an endpoint URL that will receive notifications from SC webhook', [], 'Modules.Endpointsc.Admin');
    }


    public function install()
    {
        $tablesInstalledWithSuccess = $this->createTables();
        if (!$tablesInstalledWithSuccess) {
            $this->uninstall();

            return false;
        }

        //$this->installTab();

        return parent::install()
            //&& Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', 1)
            //&& Configuration::updateValue('CROSSSELLING_NBR', 8)
            && $this->registerHook('moduleRoutes')
            ;
    }
    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function createTables()
    {
        $result = $this->getRepository()->createTables();
        if (false === $result || (is_array($result) && !empty($result))) {
            if (is_array($result)) {
                $this->addModuleErrors($result);
            }

            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall()
            //&& Configuration::deleteByName('CROSSSELLING_DISPLAY_PRICE')
            //&& Configuration::deleteByName('CROSSSELLING_NBR');
        ;
    }
    public function installDatabase() {

        $installed = true;

        $errors = $this->repository->createTables();

        if (!empty($errors)) {
            $this->addModuleErrors($errors);
            $installed = false;
        }

        return $installed;
    }

    public function getRepository() {
        if (is_null($this->repository) ) {
            try {
                $this->repository = $this->get('prestashop.module.aa_endpointforsc.carrier_mapping.repository');
            } catch (\Exception $e) {
                /** @var LegacyContext $context */
                $legacyContext = $this->get('prestashop.adapter.legacy.context');
                /** @var Context $shopContext */
                $shopContext = $this->get('prestashop.adapter.shop.context');

                $this->repository = new CarrierMappingRepository(
                    $this->get('doctrine.dbal.default_connection'),
                    SymfonyContainer::getInstance()->getParameter('database_prefix'),
                    $legacyContext->getLanguages(true, $shopContext->getContextShopID()),
                    $this->get('translator')
                );
            }
        }

        return $this->repository;
    }

    /**
     * @param array $params
     */
    public function hookModuleRoutes(array $params)
    {
        return $this->getModuleRoutes('ModuleRoutes', 'customapi');
    }

    public function getModuleRoutes($ModuleRoutes, $alias)
    {
        if ($ModuleRoutes == 'ModuleRoutes') {
            return array(
                'module-aaendpointsc-products' => array(
                    'controller' => 'notification',
                    'rule'       => $alias. '/sendcloud' ,
                    'keywords'   => array(),
                    'params'     => array(
                        'fc'     => 'module',
                        'module' => 'aa_endpointforsc',
                    ),
                )
            );
        }
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AaCarrierFooterController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AaCarrierMappingController';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = "SendCloud Carriers Mapping";
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentShipping');
        $tab->route_name = 'admin_carrier_mapping_form_view';
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AaCarrierMappingController');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }


    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTab();
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTab()
            ;
    }
    /**
     * @param array $errors
     */
    private function addModuleErrors(array $errors)
    {
        foreach ($errors as $error) {
            $this->_errors[] = $this->trans($error['key'], $error['parameters'], $error['domain']);
        }
    }


}
