<?php

namespace App\Features\Auth;

use Core\Auth\Auth;
use Core\Controller;
use Core\Database\Database;

class AuthController extends Controller
{
    public static function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Buscar usuario
        $user = Database::select(
            "SELECT * FROM users WHERE user = :username LIMIT 1",
            ['username' => $username]
        );

        if (!$user || !password_verify($password, $user[0]['password'])) {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            header('Location: /login');
            return;
        }

        Auth::login($user[0]);
        header('Location: /');
    }

    public static function showLoginForm(): string
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        return self::view('login', [
            'error' => Auth::getError() ?? null,
            'data' => [
                'username' => $_POST['username'],
                'password' => $_POST['password']
            ]
        ]);
    }
    
    public static function showRegisterForm(): string
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        return self::view('register', [
            'error' => Auth::getError() ?? null
        ]);
    }

    public static function logout(): void
    {
        Auth::logout();
        header('Location: /');
    }
}
