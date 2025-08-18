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


require_once __DIR__ . '/../../classes/Logger/CustomLogger.php';
require_once __DIR__ . '/../../classes/Shipment/Parcel.php';

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

        // do something then output the result
        $this->ajaxDie(json_encode([
            'success' => true,
            'operation' => 'get'
        ]));
    }

    protected function processPostRequest()
    {
        $rawData = file_get_contents("php://input");

        // If the data is JSON, you can decode it
        $data = json_decode($rawData, true); // true for associative array

        if ($data) {
            //echo "Received JSON data:<br>";
            //print_r($data);
            //error_log('Debug: ' . print_r($data['parcel']['carrier']['code'], true));
            //CustomLogger::log($data['parcel']['carrier']);
            CustomLogger::log($data['parcel']['shipment']);
            $code = $data['parcel']['shipment']['code'];
            //CustomLogger::log($data['parcel']['order_number']);
            //CustomLogger::log("AAAAAAAAAAAAAA\n");
            $idCarrier = Parcel::getPsIdCarrierFromScShipmentCode($code);

            $idOrder = (int) $data['parcel']['order_number'];
            $order =  new Order($idOrder);
            $oldIdCarrier = $order->id_carrier;
            $order->id_carrier = $idCarrier;
            $result = $order->update();
            CustomLogger::log( $result);





            // Update order_carrier
            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `' . _DB_PREFIX_ . 'order_carrier`
                WHERE `id_order` = ' . (int)$order->id
                );

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier((int) $id_order_carrier);
                $order_carrier->id_carrier = (int)$idCarrier;
                $result = $order_carrier->update();
                CustomLogger::log( $result);
            }

//            Hook::exec('actionCarrierUpdate', [
//                'id_carrier' => $oldIdCarrier,
//                'carrier' => new Carrier((int)$idCarrier),
//            ]);
        }//PrestaShopLogger::addLog('My variable value: ' . implode('', array_keys($_POST), true)), 1, null, 'MyModule');
        //file_put_contents('webhook_log.txt', "Received parcel update for ID:\n", FILE_APPEND);
        //var_dump($_POST);die;
        // do something then output the result
        //$order = new Order((int)$data['parcel']['order_number']);


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
