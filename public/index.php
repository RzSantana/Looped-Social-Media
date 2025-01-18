<?php

use Core\Autoload;
use Core\Routing\Router;
use Core\Session;

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/Autoload.php');

try {
    Autoload::start();
    Session::start();

    if (!Session::has('user_id') && isset($_COOKIE['remembered_user'])) {
        $user = json_decode($_COOKIE['remembered_user'], true);
        Session::set('user_id', $user['user_id']);
        Session::set('user_name', $user['user_name']);
    }

    require_once($_SERVER['DOCUMENT_ROOT']. '/app/routing/routes.php');
    
    Router::processRequest();
} catch (\Throwable $error) {
    print("Error: $error");
}
