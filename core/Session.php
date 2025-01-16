<?php

namespace Core;

/**
 * Clase Session
 * 
 * Proporciona una interfaz orientada a objetos para manejar sesiones PHP.
 * Implementa funcionalidades para gestionar datos de sesión de forma segura
 * y con tipos estrictos.
 * 
 * @package Core
 */
class Session
{
    /**
     * Inicia o reanuda una sesión de forma segura
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuración de seguridad de la sesión
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            
            if (App::env('APP_ENV') === 'production') {
                ini_set('session.cookie_secure', 1);
            }

            session_start();
            
            // Regeneramos el ID de sesión periódicamente por seguridad
            if (!isset($_SESSION['_last_regeneration'])) {
                self::regenerate();
            } elseif (time() - $_SESSION['_last_regeneration'] > 3600) {
                self::regenerate();
            }
        }
    }

    /**
     * Regenera el ID de sesión de forma segura
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_last_regeneration'] = time();
    }

    /**
     * Almacena un valor en la sesión
     * 
     * @param string $key Clave para almacenar el valor
     * @param mixed $value Valor a almacenar
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión
     * 
     * @param string $key Clave del valor a obtener
     * @param mixed $default Valor por defecto si la clave no existe
     * @return mixed Valor almacenado o valor por defecto
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verifica si existe una clave en la sesión
     * 
     * @param string $key Clave a verificar
     * @return bool true si la clave existe, false en caso contrario
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Verifica si existe una clave flash en la sesión
     * 
     * @param string $key Clave flash a verificar
     * @return bool true si la clave flash existe, false en caso contrario
     */
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash.' . $key]);
    }

    /**
     * Elimina un valor de la sesión
     * 
     * @param string $key Clave del valor a eliminar
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Obtiene y elimina un valor de la sesión
     * 
     * @param string $key Clave del valor a obtener y eliminar
     * @param mixed $default Valor por defecto si la clave no existe
     * @return mixed Valor almacenado o valor por defecto
     */
    public static function pull(string $key, mixed $default = null): mixed
    {
        $value = self::get($key, $default);
        self::remove($key);
        return $value;
    }

    /**
     * Almacena un mensaje flash en la sesión
     * 
     * @param string $key Clave del mensaje flash
     * @param mixed $value Valor del mensaje flash
     */
    public static function flash(string $key, mixed $value): void
    {
        self::set('_flash.' . $key, $value);
    }

    /**
     * Obtiene un mensaje flash de la sesión
     * 
     * @param string $key Clave del mensaje flash
     * @param mixed $default Valor por defecto si el mensaje no existe
     * @return mixed Mensaje flash o valor por defecto
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return self::pull('_flash.' . $key, $default);
    }

    /**
     * Destruye la sesión actual
     */
    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                '',
                time() - 3600,
                '/',
                '',
                true,
                true
            );
        }
    }
}