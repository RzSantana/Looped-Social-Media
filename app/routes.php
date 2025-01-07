<?php

use App\Controllers\HomeController;
use Core\Router;

Router::get('/', [HomeController::class, 'index'])->layout('layoutMain');

Router::setNotFoundCallback(function () {
    print('Página no encotrada (404)');
});