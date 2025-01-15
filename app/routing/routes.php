<?php

use App\Controllers\HomeController;
use App\Features\Auth\AuthController;
use Core\Auth\AuthMiddleware;
use Core\Routing\Router;

// Rutas públicas
Router::get('/login', [AuthController::class, 'showLoginForm'])
    ->layout('main');
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Rutas protegidas
Router::get('/', function() {
    return 'Hello ' . $_SESSION['user_name'];
})
    ->layout('main')
    ->middleware(AuthMiddleware::class);

// Cualquier otra ruta protegida seguiría el mismo patrón
// Router::get('/profile', [ProfileController::class, 'show'])
//     ->layout('main')
//     ->middleware(AuthMiddleware::class);