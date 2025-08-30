<?php
namespace PrestaShop\Module\AaEndpointForSc\Bootstrap;


use PrestaShop\Module\AaEndpointForSc\Logger\CustomLogger;
use SendCloud\BusinessLogic\DTO\WebhookDTO;
use SendCloud\BusinessLogic\Webhook\Handler\BaseWebhookHandler;
use SendCloud\BusinessLogic\Webhook\Utility\WebhookHelper;
use SendCloud\BusinessLogic\Webhook\WebhookEventHandler;
use SendCloud\BusinessLogic\Webhook\WebhookHandlerRegistry;
use Db;


class Bootstrap {

    private $repository;
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    public function init() {

        CustomLogger::log('init --------------------------------------' . time());
        //$cs = new WebhookEventHandler();

        $secret_key = '897hRT893qkA783M093ha903!';
        $rawData = file_get_contents("php://input");
        $hashed_signature = hash_hmac ( "sha256" , $rawData , $secret_key );
        $SendcloudSignature = $this->GetHeader('Sendcloud-Signature');
        $data = json_decode($rawData, true); // true for associative array

        if (hash_equals($hashed_signature, $SendcloudSignature)) {
            $code = $data['parcel']['shipment']['code'];
            CustomLogger::log('code: '. $code);
            $name = $data['parcel']['shipment']['name'];
            CustomLogger::log('name: '. $name);
            $idReferenceCarrier = $this->repository->getIdPsReference($name);
            CustomLogger::log('reference: '. $idReferenceCarrier);

            $idCarrier =  $this->repository->getIdCarrierFromReference($idReferenceCarrier);
            CustomLogger::log('id_carrier: '. $idCarrier);

            // todo: if empty else
            $idOrder = (int) $data['parcel']['order_number'];
            CustomLogger::log('order: '. $idOrder);

            $idOrderCarrier =  $this->repository->getIdOrderCarrierFromOrder($idOrder);
            CustomLogger::log('order carrier: '.  $idOrderCarrier);

            $this->repository->updateOrderCarrierInOrderTable($idCarrier, $idOrder);
            CustomLogger::log('order carrier updated in order table');
            if ($idOrderCarrier) {
                $this->repository->updateOrderCarrierInOrderCarrierTable($idCarrier, $idOrderCarrier);
                CustomLogger::log('order carrier updated in order carrier table');
            }
        } else {
            CustomLogger::log('HMAC signature verification failed. Data may be tampered or sender is not authorized.');
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