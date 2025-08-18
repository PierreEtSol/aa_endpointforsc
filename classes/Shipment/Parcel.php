<?php

require_once __DIR__ . '/../../classes/Shipment/CarrierProxy.php';


class Parcel {

    public static function getPsIdCarrierFromScShipmentCode($code)
    {
        CustomLogger::log($code);
        return (int)\CarrierProxy::CARRIER_LIST[$code];

    }
}
?>
