<?php

namespace PrestaShop\Module\AaEndpointForSc\Services\Carriers;

use Picqer\Carriers\SendCloud\Connection;
use Picqer\Carriers\SendCloud\SendCloud;
use Configuration;


class SendCloudCarriers {


    public function getSendCloudCarriers() {

        $publicKey = Configuration::get('SENDCLOUD_API_PUBLIC_KEY') ;
        $secretKey = Configuration::get('SENDCLOUD_API_SECRET_KEY') ;

        $connection = new Connection($publicKey,  $secretKey);
        $sendCloudClient = new SendCloud($connection);
        $sendCloudCarriers = [];
        try {

            $shippingMethods = $sendCloudClient->shippingMethods()->all();
            foreach ($shippingMethods as $method) {
                $sendCloudCarriers[] = [
                    'id_sc_carrier' => $method->id,
                    'name' => $method->name,
                ];
            }

            return $sendCloudCarriers;

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}