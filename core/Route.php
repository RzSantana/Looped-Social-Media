<?php

namespace Core;

class Route {
    public string $controller;
    public string $method;
    public ?string $layout;
    
    /**
     * Class constructor.
     */
    public function __construct(array $routeDefinition)
    {
        $this->controller = $routeDefinition[0];
        $this->method = $routeDefinition[1];
        $this->layout = $routeDefinition[2] ?? null; // Valor predeterminado
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }
}