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
    public static function login(array $user): void 
    {
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['user']);

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
    }

    /**
     * Verifica si hay un usuario autenticado
     */
    public static function check(): bool 
    {
        return Session::has('user_id');
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