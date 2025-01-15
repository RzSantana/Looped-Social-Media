<?php

namespace Core\Routing;

/**
 * Clase Route
 * 
 * Esta clase representa una ruta dentro de la aplicación.
 * Puede manejar tanto controladores y métodos como callbacks anónimos.
 */
class Route
{
    /**
     * @var array|callable $handler
     * Controlador y método o callback anónimo asociado a la ruta.
     */
    public $handler;

    /**
     * @var string|null $layout
     * Layout opcional para la ruta.
     */
    public ?string $layout = null;

    private ?string $middleware = null;

    /**
     * Constructor de la clase Route.
     * 
     * @param array|callable $handler Controlador y método o callback anónimo.
     */
    public function __construct(array|callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Establece el layout para la ruta.
     * 
     * @param string $layout Nombre del layout.
     * @return self Retorna la instancia actual para permitir el encadenamiento de métodos.
     */
    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    
    public function middleware(string $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function getMiddleware(): ?string
    {
        return $this->middleware;
    }
}