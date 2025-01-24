<?php

namespace App\Features\Auth;

use App\Features\User\UserRepository;
use Core\Auth\Auth;
use Core\Controller;
use Core\Exceptions\DatabaseException;
use Core\Session;
use Exception;

class AuthController extends Controller
{
    public static function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validacion basica
        if (empty($username)) {
            $errors['username'] = 'Nombre de usuario requerido';
        }

        if (empty($password)) {
            $errors['password'] = 'Contraseña requerido';
        }

        // Si se produce algún error, vuelva al formulario de login
        if (isset($errors)) {
            Session::flash('errors', $errors);
            Session::flash('username', $username);
            Session::flash('password', $password);

            header('Location: /login');
            exit;
        }

        try {
            $user = UserRepository::findByUsername($username);

            if (!$user) {
                $user = UserRepository::findByEmail($username);
            }

            if (!$user || !password_verify($password, $user['password'])) {
                Session::flash('errors', [
                    'username' => 'Usuario o email incorrecto',
                    'password' => 'Contraseña incorrecta'
                ]);
                Session::flash('username', $username);
                Session::flash('password', $password);

                header('Location: /login');
                exit;
            }

            Auth::login($user, $remember);
            header('Location: /');
        } catch (Exception $e) {
            Session::flash('error', 'Ha ocurrido un error inesperado');
            header('Location: /login');
            exit;
        }
    }

    public static function register(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['passwordConfirm'] ?? '';
        $remember = isset($_POST['remember']);

        // Validación del nombre de usuario
        if (empty($username)) {
            $errors['username'] = 'Nombre de usuario es requerido';
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            $errors['username'] = 'Nombre de usuario debe tener entre 3 y 20 caracteres';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Nombre de usuario solo puede contener letras, números y guiones bajos';
        }

        // Verificar si el nombre de usuario existe
        if (!isset($errors['username']) && UserRepository::findByUsername($username)) {
            $errors['username'] = 'Nombre de usuario ya en uso';
        }

        // Validación del correo electrónico
        if (empty($email)) {
            $errors['email'] = 'Correo electrónico es requerido';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato del correo electrónico no es válido';
        }

        // Verificar si el correo electrónico existe
        if (!isset($errors['email']) && UserRepository::findByEmail($email)) {
            $errors['email'] = 'Correo electrónico ya está registrado';
        }

        // Validación de la contraseña
        if (empty($password)) {
            $errors['password'] = 'Contraseña es requerida';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Contraseña debe tener al menos 8 caracteres';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Contraseña debe contener al menos una mayúscula';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Contraseña debe contener al menos una minúscula';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Contraseña debe contener al menos un número';
        }

        // Validación de confirmación de contraseña
        if (empty($passwordConfirm)) {
            $errors['passwordConfirm'] = 'Confirmación de la contraseña es requerida';
        } elseif ($password !== $passwordConfirm) {
            $errors['passwordConfirm'] = 'Confirmación de contraseña no coincide';
        }

        // Si se produce algún error, vuelva al formulario de registro
        if (isset($errors)) {
            Session::flash('errors', $errors);
            Session::flash('username', $username);
            Session::flash('email', $email);
            Session::flash('password', $password);
            Session::flash('passwordConfirm', $passwordConfirm);

            header('Location: /register');
            exit;
        }

        try {
            $user = UserRepository::register($username, $email, $password);

            if (!$user) {
                Session::flash('error', 'No se pudo completar el registro');
                header('Location: /register');
                exit;
            }

            Auth::login($user, $remember);
            header('Location: /');
            exit;
        } catch (DatabaseException $e) {
            Session::flash('error', 'Error al crear la cuenta');
            header('Location: /register');
            exit;
        }
    }

    public static function showLoginForm(): string
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        return self::view('login', [
            'error' => Session::getFlash('error', null),
            'errors' => Session::getFlash('errors', null),
            'username' => Session::getFlash('username', ''),
            'password' => Session::getFlash('password', ''),
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
            'errors' => Session::getFlash('errors', []),
            'username' => Session::getFlash('username', ''),
            'email' => Session::getFlash('email', ''),
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
