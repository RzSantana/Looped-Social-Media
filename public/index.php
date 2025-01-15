<?php

use Core\App;
use Core\Autoload;
use Core\Routing\Router;

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/Autoload.php');

try {
    Autoload::start();

    require_once($_SERVER['DOCUMENT_ROOT']. '/app/routing/routes.php');
    
    Router::processRequest();
} catch (\Throwable $error) {
    print("Error: $error");
}
