<?php

namespace Core;

/**
 * Clase Autoload
 * 
 * Esta clase configura el autoloader para cargar automáticamente las clases
 * según su namespace.
 */
class Autoload
{
    /**
     * Iniciador del autoloader
     * 
     * Registra una función anónima que convierte el nombre completo de una clase
     * en una ruta de archivo, utilizando el namespace como estructura de directorios.
     */
    public static function start(): void
    {
        spl_autoload_register(function ($class) {
            // Divide el nombre completo de la clase en parte según el namespace
            $directoris = explode("\\", $class);

            // Convierte a minúsculas todas las partes del nombre de la clase excepto la última.
            foreach ($directoris as $key => $directori) {
                if ($key !== count($directoris) - 1) {
                    $directoris[$key] = strtolower($directori);
                }

                $path = $_SERVER['DOCUMENT_ROOT'] . '/' . implode('/', $directoris) . '.php';

                if (file_exists($path)) {
                    require_once($path);
                } else {
                    throw new \Exception("Clase no encontrada: $class en $path");
                }
            }
        });
    }
}
