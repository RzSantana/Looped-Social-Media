<?php

namespace Core\Auth;

use Core\Session;

/**
 * Sistema de autenticación base del framework
 * 
 * Proporciona la funcionalidad básica de autenticación que cualquier
 * aplicación necesitará, manteniendo un diseño simple pero efectivo.
 */
class Auth
{
    /**
     * Inicia la sesión del usuario
     */
    public static function login(array $user, bool $remember = false): void
    {
        // Guardar usuario en la session
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['user']);

        // Si se marco recorda se guarda el usuario en cookies por 30 dias
        if ($remember) {
            setcookie(
                'remembered_user',
                json_encode([
                    'user_id' => $user['id'],
                    'user_name' => $user['user'],
                ]),
                time() + (30 * 24 * 60 * 60),
                '/'
            );
        }

        if (session_status() == PHP_SESSION_ACTIVE) {
            Session::regenerate();
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public static function logout(): void
    {
        Session::destroy();

        // Eliminar la cookie si existe
        if (isset($_COOKIE['remembered_user'])) {
            setcookie('remembered_user', '', time() - 3600, '/');
        }
    }

    /**
     * Verifica si hay un usuario autenticado
     */
    public static function check(): bool
    {
        return Session::has('user_id') || isset($_COOKIE['remembered_user']);
    }

    /**
     * Obtiene el ID del usuario autenticado
     */
    public static function id(): ?int
    {
        return Session::get('user_id', null);
    }

    /**
     * Obtiene el nombre del usuario autenticado
     */
    public static function user(): ?string
    {
        return Session::get('user_name', null);
    }
}
