<?php

namespace PrestaShop\Module\AaEndpointForSc\Services\Carriers;

use Picqer\Carriers\SendCloud\Connection;
use Picqer\Carriers\SendCloud\SendCloud;
use Configuration;


class SendCloudCarriers {

//    /**
//     * @var Configuration
//     */
//    protected $configService;
//    /**
//     * @return Configuration
//     */
//    protected function getConfigService()
//    {
//        if ($this->configService === null) {
//            $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
//        }
//
//        return $this->configService;
//    }


    public function getSendCloudCarriers() {
  //      $publicKey = $this->getConfigService()->getPublicKey() ;
    //    $secretKey = $this->getConfigService()->getSecretKey() ;

        $publicKey = Configuration::get('SENDCLOUD_API_PUBLIC_KEY') ;
        $secretKey = Configuration::get('SENDCLOUD_API_SECRET_KEY') ;

        $connection = new Connection($publicKey,  $secretKey);
        $sendCloudClient = new SendCloud($connection);
        $sendCloudCarriers = [];
        try {

            $shippingMethods = $sendCloudClient->shippingMethods()->all();
            //echo "<pre>";
            //var_dump($shippingMethods);die;
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