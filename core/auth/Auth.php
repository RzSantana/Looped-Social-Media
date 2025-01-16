<?php

namespace Core\Auth;

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
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['user'];
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public static function logout(): void 
    {
        session_destroy();
    }

    /**
     * Verifica si hay un usuario autenticado
     */
    public static function check(): bool 
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Obtiene el ID del usuario autenticado
     */
    public static function id(): ?int 
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el nombre del usuario autenticado
     */
    public static function user(): ?string 
    {
        return $_SESSION['user_name'] ?? null;
    }

    public static function getError(): ?string 
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        return $error;
    }
}