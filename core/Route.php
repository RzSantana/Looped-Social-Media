<?php

namespace Core;

class Route {
    /**
     * @var string $controller Nombre del controlador asociado a la ruta.
     */
    public string $controller;

    /**
     * @var string $method Nombre del método del controlador asociado a la ruta.
     */
    public string $method;

    /**
     * @var string|null $layout Nombre del layout asociado a la ruta, o null si no se especifica.
     */
    public ?string $layout;

    /**
     * Constructor de la clase.
     * 
     * @param array $routeDefinition Definición de la ruta, que incluye el controlador, el método y opcionalmente el layout.
     */
    public function __construct(array $routeDefinition)
    {
        $this->controller = $routeDefinition[0];
        $this->method = $routeDefinition[1];
        $this->layout = $routeDefinition[2] ?? null; // Valor predeterminado
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
}