<?php

use App\Controllers\HomeController;
use Core\Router;

Router::get('/', [HomeController::class, 'index']);

Router::setNotFoundCallback(function () {
    print('Página no encotrada (404)');
});