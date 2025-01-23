<?php

namespace Core;

use Exception;
use ReflectionClass;

/**
 * Clase base abstracta para todos los controladores
 */
abstract class Controller
{
    /**
     * Renderiza una vista con los datos proporcionados
     *
     * Este método busca las vistas en el directorio del módulo que las solicita.
     * Por ejemplo, si AuthController llama a view('views/login'), buscará en:
     * /app/features/Auth/views/login.view.php
     */
    protected static function view(string $template, array $data = []): string
    {
        // Obtenemos la clase que llamó a view()
        $callingClass = static::class;

        // Usamos Reflection para obtener el directorio del módulo
        $reflection = new ReflectionClass($callingClass);
        $classPath = dirname($reflection->getFileName());

        // Construimos la ruta completa a la vista
        $viewPath = $classPath . '/' . $template . '.view.php';

        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: $template en $viewPath");
        }

        // Extraemos las variables para que estén disponibles en la vista
        extract($data);

        // Capturamos la salida de la vista
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }
}
