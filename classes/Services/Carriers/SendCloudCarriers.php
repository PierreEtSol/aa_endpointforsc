<?php

namespace PrestaShop\Module\AaEndpointForSc\Services\Carriers;

use Picqer\Carriers\SendCloud\Connection;
use Picqer\Carriers\SendCloud\SendCloud;



class SendCloudCarriers {

    public function getSendCloudCarriers() {

        $connection = new Connection('9a4d81fe-7a4d-4d6b-b84b-f4cef6080db0', 'e6c8e697c6f3406bbcc3c66b718c4d4a');
        $sendCloudClient = new SendCloud($connection);
        $sendCloudCarriers = [];
        try {

            $shippingMethods = $sendCloudClient->shippingMethods()->all();
            foreach ($shippingMethods as $method) {
                $sendCloudCarriers[] = [
                    'id_sc_carrier' => $method->id,
                    'code' => $method->name,
                ];
            }

            return $sendCloudCarriers;

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}