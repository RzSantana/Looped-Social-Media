<?php

namespace App\Features\Auth;

use App\Features\User\UserRepository;
use Core\Auth\Auth;
use Core\Controller;
use Core\Database\Database;
use Core\Session;

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
            Session::flash('error', 'Usuario o contraseña incorrectos');
            Session::flash('username', $username);
            Session::flash('password', $password);

            header('Location: /login');
            exit;
        }

        Auth::login($user[0]);
        header('Location: /');
    }

    public static function register(): void
    {
        $username = $_POST['username'] ?? '';
        $mail = $_POST['mail'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['passwordConfirm'] ?? '';

        // Validadion del nombre de usuario
        if (empty($username)) {
            Session::flash('error', 'El nombre de usuario es requerido');
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            Session::flash('error', 'El nombre de usuario debe tener entre 3 y 20 caracteres');
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            Session::flash('error', 'El nombre de usuario solo puede contener letras, números y guiones bajos');
        }

        // Verificar si el nombre de usuario existe
        if (UserRepository::findByUsername($username)) {
            $errors[] = 'Este nombre de usuario ya está en uso';
        }
        $existingUser = Database::select(
            "SELECT id FROM users WHERE user = :username LIMIT 1",
            ['username' => $username]
        );
        if (!empty($existingUser)) {
            Session::flash('error', 'Este nombre de usuario ya está en uso');
        }

        // Validadion del mail
        if (empty($mail)) {
            Session::flash('error', 'El correo electrónico es requerido');
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'El formato del correo electrónico no es válido');
        }

        // Verificar si el correo electronico existe
        $existingEmail = Database::select(
            "SELECT id FROM users WHERE mail = :mail LIMIT 1",
            ['mail' => $mail]
        );
        if (!empty($existingEmail)) {
            Session::flash('error', 'Este correo electrónico ya está registrado');
        }

        // Validacion de la contraseña
        if (empty($password)) {
            Session::flash('error', 'La contraseña es requerida');
        } elseif (strlen($password) < 8) {
            Session::flash('error', 'La contraseña debe tener al menos 8 caracteres');
        } elseif (!preg_match('/[A-Z]/', $password)) {
            Session::flash('error', 'La contraseña debe contener al menos una mayúscula');
        } elseif (!preg_match('/[a-z]/', $password)) {
            Session::flash('error', 'La contraseña debe contener al menos una minúscula');
        } elseif (!preg_match('/[0-9]/', $password)) {
            Session::flash('error', 'La contraseña debe contener al menos un número');
        }

        // Valicación de confimacion de contraseña
        if ($password !== $passwordConfirm) {
            Session::flash('error', 'Las contraseñas no coinciden');
        }

        // Si se produce algún error, vuelva al formulario de registro
        if (!Session::hasFlash('error')) {
            Session::flash('username', $username);
            Session::flash('mail', $mail);
            Session::flash('password', $password);
            Session::flash('passwordConfirm', $passwordConfirm);

            header('Location: /register');
            exit;
        }


        header('Location: /');
    }

    public static function showLoginForm(): string
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        return self::view('login', [
            'error' => Session::getFlash('error', null),
            'username' => Session::getFlash('username', ''),
            'password' => Session::getFlash('password', ''),
            'visibility' => Session::get('visibility', false),
        ]);
    }

    public static function showRegisterForm(): string
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        return self::view('register', [
            'error' => Session::getFlash('error', null),
            'username' => Session::getFlash('username', ''),
            'mail' => Session::getFlash('mail', ''),
            'password' => Session::getFlash('password', ''),
            'passwordConfirm' => Session::getFlash('passwordConfirm', ''),

        ]);
    }

    public static function logout(): void
    {
        Auth::logout();
        header('Location: /');
    }
}
