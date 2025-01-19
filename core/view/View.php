<?php

namespace Core\View;

class View
{
    /**
     * Renderiza un componente y retorna su contenido como string
     * 
     * @param string $componentPath Ruta al componente relativa a /resources/components
     * @param array $data Datos que se pasarán al componente
     */
    public static function component(string $componentPath, array $data = []): string
    {
        // Extraemos los datos para que estén disponibles en el componente
        extract($data);

        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/resources/components/' . $componentPath . '.component.php';

        if (!file_exists($fullPath)) {
            throw new \Exception("Component not found: {$componentPath}");
        }

        ob_start();
        require $fullPath;
        return ob_get_clean();
    }

    /**
     * Renderiza una página de error y establece el código de respuesta HTTP
     * 
     * @param string $errorPage Nombre de la página de error
     * @param int $statusCode Código de estado HTTP
     * @param array $data Datos adicionales para la página de error
     * @throws \Exception Si la página de error no existe
     */
    public static function error(string $errorPage, int $statusCode = 404, array $data = []): string
    {
        // Establecer el código de estado HTTP
        http_response_code($statusCode);

        // Extraer datos para que estén disponibles en la vista de error
        extract($data);

        // Construir ruta completa al archivo de error
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/resources/errors/' . $errorPage . '.view.php';

        // Verificar si el archivo de error existe
        if (!file_exists($fullPath)) {
            // Página de error por defecto si no se encuentra la específica
            $defaultErrorPath = $_SERVER['DOCUMENT_ROOT'] . '/resources/errors/default.view.php';

            if (file_exists($defaultErrorPath)) {
                $fullPath = $defaultErrorPath;
            } else {
                // Si no existe ni la página específica ni la por defecto, lanzar excepción
                throw new \Exception("Error page not found: {$errorPage}");
            }
        }

        // Capturar la salida del buffer
        ob_start();
        require $fullPath;
        return ob_get_clean();
    }
}
