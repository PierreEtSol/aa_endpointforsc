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


        $senderAddressesIds = $this->getSenderAddressesIds($sendCloudClient);
        if (!is_array($senderAddressesIds)) {
            return [];
        }
        $sendCloudCarriers = [];
        foreach($senderAddressesIds as $senderAddressId) {
            $sendCloudCarriers = array_merge($sendCloudCarriers,  $this->getShippingMethods($sendCloudClient, $senderAddressId));
        }

        $sendCloudCarriers = array_unique($sendCloudCarriers, SORT_REGULAR);

        return $sendCloudCarriers;
    }

    public function getSenderAddressesIds($sendCloudClient) {
        try {
            $senderAddresses = $sendCloudClient->senderAddresses()->all();
            return array_column($senderAddresses, 'id');

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getShippingMethods($sendCloudClient, $senderAddressId) {
        $senderShippingMethods = [];
        try {
            $shippingMethods = $sendCloudClient->shippingMethods()->all(['sender_address' => $senderAddressId]);
            foreach ($shippingMethods as $method) {
                $senderShippingMethods[] = [
                    'id_sc_carrier' => $method->id,
                    'name' => $method->name,
                ];
            }

            return $senderShippingMethods;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}