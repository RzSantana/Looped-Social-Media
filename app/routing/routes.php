<?php

use App\Features\Auth\AuthController;
use App\Features\Feed\FeedController;
use App\Features\New\NewController;
use App\Features\Post\PostController;
use App\Features\Search\SearchController;
use App\Features\User\UserController;
use Core\Auth\AuthMiddleware;
use Core\Routing\Router;

// Rutas públicas =================================================

// Rutas para el registro
Router::get('/register', [AuthController::class, 'showRegisterForm'])
    ->layout('auth');
Router::post('/register', [AuthController::class, 'register']);

// Rutas para el login
Router::get('/login', [AuthController::class, 'showLoginForm'])
    ->layout('auth');
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Rutas protegidas ===============================================
Router::get('/', [FeedController::class, 'showFeed'])
    ->layout('main', ['activeHome' => true])
    ->middleware(AuthMiddleware::class);

// Ruta para la busqueda
Router::get('/search', [SearchController::class, 'showSearch'])
    ->layout('main', [
        'activeSearch' => true,
        'search' => true,
        'valueSearch' => $_GET['user'] ?? ''
    ])
    ->middleware(AuthMiddleware::class);

// Ruta para crear un nuevo post
Router::get('/new', [NewController::class, 'showNew'])
    ->layout('main', ['activeNew' => true])
    ->middleware(AuthMiddleware::class);
Router::post('/new', [NewController::class, 'createPost'])
    ->middleware(AuthMiddleware::class);

// Ruta para ver perfil de usuario
Router::get('/profile', [UserController::class, 'showProfile'])
    ->layout('main')
    ->middleware(AuthMiddleware::class);
Router::get('/profile/edit', [UserController::class, 'showProfileEdit'])
    ->layout('main')
    ->middleware(AuthMiddleware::class);
Router::get('/user/:id', [UserController::class, 'showUserProfile'])
    ->layout('main')
    ->middleware(AuthMiddleware::class);

// Ruta para seguir y dejar de seguir a un usuario
Router::post('/follow/:id', [UserController::class, 'follow'])
    ->middleware(AuthMiddleware::class);
Router::post('/unfollow/:id', [UserController::class, 'unfollow'])
    ->middleware(AuthMiddleware::class);

// Ruta para visualiar un post espeficico
Router::get('/post/:id', [PostController::class, 'showPost'])
    ->layout('main')
    ->middleware(AuthMiddleware::class);
// Rutas para likes y dislikes
Router::get('/post/like/:id', [FeedController::class, 'toggleLike'])
    ->middleware(AuthMiddleware::class);
Router::get('/post/dislike/:id', [FeedController::class, 'toggleDislike'])
    ->middleware(AuthMiddleware::class);


Router::setNotFoundCallback(function () {
    header('Location: /');
});
