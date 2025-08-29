<?php
namespace PrestaShop\Module\AaEndpointForSc\Bootstrap;


use PrestaShop\Module\AaEndpointForSc\Logger\CustomLogger;
use SendCloud\BusinessLogic\DTO\WebhookDTO;
use SendCloud\BusinessLogic\Webhook\Handler\BaseWebhookHandler;
use SendCloud\BusinessLogic\Webhook\Utility\WebhookHelper;
use SendCloud\BusinessLogic\Webhook\WebhookEventHandler;
use SendCloud\BusinessLogic\Webhook\WebhookHandlerRegistry;

class Bootstrap {

    private $repository;
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    public function init() {

        CustomLogger::log('init --------------------------------------');
        //$cs = new WebhookEventHandler();

        $secret_key = '897hRT893qkA783M093ha903!';
        $rawData = file_get_contents("php://input");
        $hashed_signature = hash_hmac ( "sha256" , $rawData , $secret_key );
        $SendcloudSignature = $this->GetHeader('Sendcloud-Signature');
        $data = json_decode($rawData, true); // true for associative array

        if (isset($data['parcel']['shipment']['code'])) {
            $code = $data['parcel']['shipment']['code'];
            CustomLogger::log('code: '. $code);
            $name = $data['parcel']['shipment']['name'];
            CustomLogger::log('name: '. $name);
            $idReferenceCarrier = $this->repository->getIdPsReference($name);
            CustomLogger::log('reference: '. $idReferenceCarrier);


            $idCarrier = Db::getInstance()->getValue('
                SELECT `id_carrier`
                FROM `' . _DB_PREFIX_ . 'carrier`
                WHERE `id_reference` = ' . (int)$idReferenceCarrier
            );

            CustomLogger::log('id_carrier: '. $idCarrier);

            // todo: if empty else
            $idOrder = (int) $data['parcel']['order_number'];

            // Update order_carrier
            $order =  new Order($idOrder);
            $order->id_carrier = $idCarrier;
            $order->update();

            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `' . _DB_PREFIX_ . 'order_carrier`
                WHERE `id_order` = ' . (int)$order->id
            );

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier((int) $id_order_carrier);
                $order_carrier->id_carrier = (int)$idCarrier;
                $order_carrier->update();
            }

        }


    }
    protected function GetHeader($myheader) {
        if (isset($_SERVER[$myheader])) {
            return $_SERVER[$myheader];
        } else {
            $headers = apache_request_headers();
            if (isset($headers[$myheader])) {
                return $headers[$myheader];
            }
        }
        return '';
    }

}