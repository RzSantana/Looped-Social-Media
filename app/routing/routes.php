<?php

use App\Features\Auth\AuthController;
use App\Features\Feed\FeedController;
use App\Features\Search\SearchController;
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
    ->layout('main', ['activeHome' => true])
    ->middleware(AuthMiddleware::class);

Router::get('/search', [SearchController::class, 'showSearch'])
    ->layout('main', ['activeSearch' => true, 'search' => true])
    ->middleware(AuthMiddleware::class);


// Rutas para likes y dislikes
Router::get('/post/like/:id', [FeedController::class, 'toggleLike'])
    ->middleware(AuthMiddleware::class);
Router::get('/post/dislike/:id', [FeedController::class, 'toggleDislike'])
    ->middleware(AuthMiddleware::class);

// Cualquier otra ruta protegida seguiría el mismo patrón
// Router::get('/profile', [ProfileController::class, 'show'])
//     ->layout('main')
//     ->middleware(AuthMiddleware::class);