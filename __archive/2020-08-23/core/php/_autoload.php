<?php

// Autoload File which will load all of the required files
$vendorPluginAutoload =  '../../vendor/autoload.php';
if (file_exists($vendorPluginAutoload)) {
    require_once $vendorPluginAutoload;
}

require_once 'connection.php';
require_once 'config.php';
require_once 'server-side-validation.php';
require_once 'error-handler.php';
require_once 'generic-functions.php';

