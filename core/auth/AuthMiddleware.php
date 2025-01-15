<?php

namespace Core\Auth;

use Core\Auth\Auth;

class AuthMiddleware
{
    public function handle(): void
    {
        // Si el usuario no está autenticado, redirigimos al login
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
    }
}