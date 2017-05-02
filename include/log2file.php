<?php

/* 
 * This is a convenient to include file with additional log2file function
 */

function log2file($logText, $fileName = null) {
    date_default_timezone_set('Europe/Berlin');
    $file = '/var/www/osticket/uploads/test'.$fileName.'.txt';
    $body = date('Y-m-d H:i:s')." $logText\n";
    file_put_contents($file, $body, FILE_APPEND);
}
?>