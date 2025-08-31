<?php

namespace PrestaShop\Module\AaEndpointForSc\Logger;

class CustomLogger {
    const DEFAULT_LOG_FILE ="sendcloud_webhook.log";
    public static function log($message){
        $fileDir = _PS_ROOT_DIR_ . '/var/log/';
        $fileName = self::DEFAULT_LOG_FILE;
        ob_start();
        var_dump($message);
        $dumpContent = ob_get_clean();

        return file_put_contents($fileDir . $fileName, $dumpContent, FILE_APPEND);
    }
}
?>
