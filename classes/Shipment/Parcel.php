<?php
namespace PrestaShop\Module\AaEndpointForSc\Shipment;

use PrestaShop\Module\AaEndpointForSc\Shipment\CarrierProxy;
use PrestaShop\Module\AaEndpointForSc\Logger\CustomLogger;
//require_once __DIR__ . '/../../classes/Shipment/CarrierProxy.php';


class Parcel {

    public static function getPsIdCarrierFromScShipmentCode($code)
    {
        //CustomLogger::log($code);
        return (int)CarrierProxy::CARRIER_LIST[$code];

    }
}
?>
