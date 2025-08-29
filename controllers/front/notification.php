<?php
/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    //var_dump('hi');die;
    require_once $autoloadPath;
}

use PrestaShop\Module\AaEndpointForSc\Bootstrap\Bootstrap;

//require_once __DIR__ . '/../../classes/Bootstrap/Bootstrap.php';
//require_once __DIR__ . '/../../classes/Shipment/Parcel.php';

class Aa_endpointforscNotificationModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->processGetRequest();
                break;
            case 'POST':
                $this->processPostRequest();
                break;
            case 'PATCH': // you can also separate these into their own methods
            case 'PUT':
                $this->processPutRequest();
                break;
            case 'DELETE':
                $this->processDeleteRequest();
                break;
            default:
                // throw some error or whatever
        }
    }

    protected function processGetRequest()
    {



        $this->ajaxDie(json_encode([
            'success' => true,
            'operation' => 'get'
        ]));
    }

    protected function processPostRequest()
    {
        $kernel = new \AppKernel('prod', false);
        $kernel->boot();
        //dump($kernel->getContainer()); // Container OK, we can call $kernel->getContainer()->get('service')
        $container = $kernel->getContainer();
//
//        // Retrieve a service by its ID
//        // Replace 'your_company.your_module.your_service_id' with the actual ID of your service
        $bootstrap = $container->get('prestashop.module.aa_endpointforsc.carrier_mapping.bootstrap');
        // do something then output the result
        $bootstrap->init();


        $this->ajaxDie(json_encode([
            'success' => true,
            'operation' => 'post'
        ]));
    }

    protected function processPutRequest()
    {
        // do something then output the result
        $this->ajaxDie(json_encode([
            'success' => true,
            'operation' => 'put'
        ]));
    }

    protected function processDeleteRequest()
    {
        // do something then output the result
        $this->ajaxDie(json_encode([
            'success' => true,
            'operation' => 'delete'
        ]));
    }
}
