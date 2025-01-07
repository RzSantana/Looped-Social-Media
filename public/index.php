<?php

use Core\Autoload;
use Core\Router;

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/Autoload.php');

try {
    Autoload::start();

    require_once($_SERVER['DOCUMENT_ROOT']. '/app/routes.php');
    
    Router::processRequest();
} catch (\Throwable $error) {
    print("Error: $error");
}
