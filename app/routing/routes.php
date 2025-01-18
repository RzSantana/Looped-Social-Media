<?php

use App\Features\Auth\AuthController;
use App\Features\Feed\FeedController;
use Core\Auth\AuthMiddleware;
use Core\Routing\Router;

// Rutas públicas
Router::get('/register', [AuthController::class, 'showRegisterForm'])
    ->layout('auth');
Router::post('/register', [AuthController::class, 'register']);

Router::get('/login', [AuthController::class, 'showLoginForm'])
    ->layout('auth');
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Rutas protegidas
Router::get('/', [FeedController::class, 'showFeed'])
    ->layout('main')
    ->middleware(AuthMiddleware::class);

// Cualquier otra ruta protegida seguiría el mismo patrón
// Router::get('/profile', [ProfileController::class, 'show'])
//     ->layout('main')
//     ->middleware(AuthMiddleware::class);