<?php

namespace Core;

use Exception;
use InvalidArgumentException;

/**
 * Clase Router
 * 
 * Esta clase maneja el enrutamiento de solicitudes HTTP dentro 
 * de la aplicación. 
 */
class Router
{
    /**
     * @var Route[] $routes
     * Almacena las rutas registradas, organizadas por método HTTP.
     */
    public static array $routes = [];

    /**
     * @var callable $notFoundCallback
     * Callback que se ejecuta cuando no se encuentra una ruta. (404)
     */
    private static $notFoundCallback;

    /**
     * Procesa la solicitud HTTP actual.
     * 
     * Determina la ruta solicitada y ejecuta el controlador correspondiente.
     * Si la ruta no existe, se ejecuta el callback de 404.
     * 
     * @return void
     */
    public static function processRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');

        foreach (self::$routes[$method] as $route => $routeObject) {
            assert(
                $routeObject instanceof Route,
                new Exception("El objeto de la ruta debe ser una instancia de la clase Route")
            );

            // Convertir la ruta en un patrón de expresión regular 
            $pattern = preg_replace('/:[^\/]+/', '([^/]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches);

                // Extraemos las definiciones de la ruta 
                $controllerClass = $routeObject->controller;
                $controllerMethod = $routeObject->method;
                $layout = $routeObject->layout ?? null;

                if (!method_exists($controllerClass, $controllerMethod)) {
                    throw new InvalidArgumentException("El método $controllerMethod no existe en el controlador $controllerClass");
                }

                // Obtener el contenido renderizado del controlador
                $content = call_user_func_array([$controllerClass, $controllerMethod], $matches);

                // Rederizar el layout con el contenido si hay un layout
                if ($layout) {
                    self::renderLayout($layout, $content);
                } else {
                    print($content);
                }
                return;
            }
        }

        if (self::$notFoundCallback) {
            call_user_func(self::$notFoundCallback);
        } else {
            http_response_code(404);
            print("404 Not Found");
        }
    }

    public static function renderLayout(string $layout, string $content): void
    {
        $layoutFile = $_SERVER['DOCUMENT_ROOT'] . '/app/layouts/' . $layout . '.php';

        if (file_exists($layoutFile)) {
            $title = 'Title';
            $slot = $content;
            require_once($layoutFile);
        } else {
            throw new InvalidArgumentException("El layout $layout no existe");
        }
    }

    /**
     * Añade una nueva ruta a la lista de rutas.
     * 
     * @param string $method Método HTTP (GET, POST, PUT, DELETE)
     * @param string $uri URI de la ruta.
     * @param array $routeDefinition Definición del controlador para la ruta.
     * @return void
     */
    public static function addRoute(string $method, string $uri, array $routeDefinition): void
    {
        $uri = trim($uri, '/');
        $route = new Route($routeDefinition);
        self::$routes[strtoupper($method)][$uri] = $route;
    }

    /** 
     * Añade una ruta GET.
     * 
     * @param string $uri URI de la ruta.
     * @param array $routeDefinition Definición del controlador para la ruta.
     * @return void
     */
    public static function get(string $uri, array $routeDefinition): void
    {
        $uri = trim($uri, '/');
        $route = new Route($routeDefinition);
        self::$routes['GET'][$uri] = $route;
    }

    /** 
     * Añade una ruta POST. 
     * 
     * @param string $uri URI de la ruta.
     * @param array $routeDefinition Definición del controlador para la ruta.
     * @return void
     */
    public static function post(string $uri, array $routeDefinition): void
    {
        $uri = trim($uri, '/');
        $route = new Route($routeDefinition);
        self::$routes['POST'][$uri] = $route;
    }

    /**
     * Añade una ruta PUT.
     * 
     * @param string $uri URI de la ruta.
     * @param array $routeDefinition Definición del controlador para la ruta.
     * @return void
     */
    public static function put(string $uri, array $routeDefinition): void
    {
        $uri = trim($uri, '/');
        $route = new Route($routeDefinition);
        self::$routes['PUT'][$uri] = $route;
    }

    /**
     * Añade una ruta DELETE. 
     * 
     * @param string $uri URI de la ruta.
     * @param array $routeDefinition Definición del controlador para la ruta.
     * @return void
     */
    public static function delete(string $uri, array $routeDefinition): void
    {
        $uri = trim($uri, '/');
        $route = new Route($routeDefinition);
        self::$routes['DELETE'][$uri] = $route;
    }

    /** 
     * Establece la función de callback para manejar rutas no encontradas (404).
     * 
     * @param callable $callback Función de callback para 404.
     * @return void
     */
    public static function setNotFoundCallback(callable $callback): void
    {
        self::$notFoundCallback = $callback;
    }
}
