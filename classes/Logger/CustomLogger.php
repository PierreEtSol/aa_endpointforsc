<?php

namespace PrestaShop\Module\AaEndpointForSc\Logger;

class CustomLogger {
    const DEFAULT_LOG_FILE ="prestashop_system.log";
    public static function log($message){
        $fileDir = _PS_ROOT_DIR_ . '/log/';
        $fileName = self::DEFAULT_LOG_FILE;
        //var_dump($fileName);
        //if(is_array($message) || is_object($message)){$message = print_r($message, true);}

        ob_start();

// Perform the var_dump()
        var_dump($message);

// Get the buffered content
        $dumpContent = ob_get_clean();


        return file_put_contents($fileDir . $fileName, $dumpContent, FILE_APPEND);
    }
}
?>
