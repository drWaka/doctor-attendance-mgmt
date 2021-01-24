<?php

// Autoload File which will load all of the required files
$vendorAutoload = [
    '../../vendor/autoload.php',
    '../vendor/autoload.php',
    'vendor/autoload.php'
];
foreach($vendorAutoload as $path) {
    if (file_exists($path)) {
        require_once $path;
    }
}

require_once '_load-env-file.php';
require_once 'connection.php';
require_once 'config.php';
require_once 'server-side-validation.php';
require_once 'error-handler.php';
require_once 'generic-functions.php';
require_once 'generic-mail-function.php';

