<?php

use App\Controllers\HomeController;
use Core\Router;

Router::get('/', [HomeController::class, 'index'])->layout('layoutMain');

Router::get('/login', function () {
    return "<h1>Login</h1>";
});

Router::setNotFoundCallback(function () {
    print('Página no encotrada (404)');
});

/**
 * Router::get('/login')
 *  ->name('login')
 *  ->prefix('/public')
 *  ->middleware([AuthMiddleware::class])
 *  ->layout('layoutMain') 
 *  ->action(HomeController::class, 'index')
 */
