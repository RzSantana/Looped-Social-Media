<?php

namespace Core;

use Exception;

/**
 * Clase base abstracta para todos los controladores.
 */
abstract class Controller
{
    /**
     * Renderiza una plantilla con los datos proporcionados.
     * 
     * @param string $template Nombre de la plantilla (sin extensión).
     * @param array $data Datos a ser extraídos y utilizados en la plantilla.
     * @throws Exception Si la plantilla no se encuentra.
     * @return string El contenido renderizado de la plantilla.
     */
    private static function render(string $template, array $data): string {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/app/views/' . $template . '.php';

        if (file_exists($file)) {
            extract($data);
            ob_start();
            require_once($file);
            return ob_get_clean();
        } else {
            throw new Exception("Plantilla no encontrada: $template");
        }
    }

    /**
     * Devuelve la vista renderizada.
     * 
     * @param string $viewName Nombre de la vista (sin extensión).
     * @param array $data Datos a ser extraídos y utilizados en la vista.
     * @return string El contenido renderizado de la vista.
     */
    protected static function view(string $viewName, array $data = []): string {
        return self::render($viewName, $data);
    }
}
