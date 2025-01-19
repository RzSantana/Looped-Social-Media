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
}
