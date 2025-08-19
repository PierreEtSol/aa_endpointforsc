<?php

namespace PrestaShop\Module\AaEndpointForSc\Shipment;


interface CarrierProxy
{
    const CARRIER_LIST = [
        'bpost:international-bpackworldbusiness/kg=0-2' => 1194, //Bpost Home deliveries
        'dpd:home/kg=0-31.5' => 1163,
        'bpost:athome-bpack24hpro/kg=0-10' => 1194,
        'bpost:international-bpackworldbusiness/kg=10-20' => 1194
    ];
}
