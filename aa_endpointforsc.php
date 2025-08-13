<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Aa_Endpointforsc extends Module
{
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
        //var_dump('hi');die;
        return parent::install()
            && $this->registerHook('moduleRoutes')
        ;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * @param array $params
     */
    public function hookModuleRoutes(array $params)
    {
        //var_dump($this->getModuleRoutes('ModuleRoutes', 'customapi'));die;
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

}
