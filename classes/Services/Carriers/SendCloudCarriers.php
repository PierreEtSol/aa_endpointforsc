<?php

namespace PrestaShop\Module\AaEndpointForSc\Services\Carriers;

use Picqer\Carriers\SendCloud\Connection;
use Picqer\Carriers\SendCloud\SendCloudClient;



class SendCloudCarriers {

    public function getSendCloudCarriers() {

        $connection = new Connection('YOUR_PUBLIC_KEY', 'YOUR_SECRET_KEY');
        $sendCloudClient = new SendCloudClient($connection);
    }
}