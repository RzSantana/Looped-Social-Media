<?php

namespace Core;

use Exception;

abstract class Controller
{
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

    protected static function view(string $viewName, array $data = []): string {
        return self::render($viewName, $data);
    }
}
