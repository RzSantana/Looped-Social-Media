<?php

use Core\Routing\Router;

Router::get('/', function () {
    return "<h1>Main</h1>";
});

Router::get('/login', function () {
    return "<h1>Login</h1>";
});

Router::get('user/:name', function(string $name) {
    return "<h1>User $name</h1>";
});

Router::setNotFoundCallback(function () {
    print "Página no encotrada (404)"; // TODO: Cambinar a retorno de string's o view's 
});

/**
 * Router::get('/login')
 *  ->name('login')
 *  ->prefix('/public')
 *  ->middleware([AuthMiddleware::class])
 *  ->layout('layoutMain') 
 *  ->action(HomeController::class, 'index')
 */
