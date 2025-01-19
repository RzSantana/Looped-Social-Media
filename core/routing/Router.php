<?php

namespace Core\Routing;

use Core\Session;
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

    private static array $middleware = [];

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
            if (preg_match("#^$pattern$#", $uri, $params)) {
                array_shift($params);

                // Si la ruta tiene middleware, lo ejecutamos
                if ($middleware = $routeObject->getMiddleware()) {
                    $middlewareInstance = new $middleware();
                    $middlewareInstance->handle();
                }

                // Extraemos las definiciones de la ruta 
                $handler = $routeObject->handler;
                $layout = $routeObject->layout ?? null;
                $layoutData = $routeObject->layoutData ?? [];

                if (is_callable($handler)) {
                    $content = call_user_func_array($handler, $params);
                } else {
                    [$controllerClass, $controllerMethod] = $handler;

                    if (!method_exists($controllerClass, $controllerMethod)) {
                        throw new InvalidArgumentException("El método $controllerMethod no existe en el controlador $controllerClass");
                    }

                    // Obtener el contenido renderizado del controlador
                    $content = call_user_func_array([$controllerClass, $controllerMethod], $params);
                }

                // Renderizar el layout con el contenido si hay un layout
                if ($layout) {
                    self::renderLayout($layout, $content, $layoutData);
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

    public static function middleware(string $middlewareClass): Route
    {
        // Obtenemos la última ruta añadida
        $lastMethod = array_key_last(self::$routes);
        $lastUri = array_key_last((array) self::$routes[$lastMethod]);
        $route = self::$routes[$lastMethod][$lastUri];

        // Añadimos el middleware a la ruta
        $route->middleware($middlewareClass);

        return $route;
    }

    /**
     * Renderiza un layout con el contenido proporcionado.
     * 
     * @param string $layout Nombre del archivo de layout (sin extensión).
     * @param string $content Contenido a ser insertado en el layout.
     * @throws InvalidArgumentException Si el archivo de layout no existe.
     * @return void
     */
    public static function renderLayout(string $layout, string $content, array $data = []): void
    {
        $layoutFile = $_SERVER['DOCUMENT_ROOT'] . '/resources/layouts/' . $layout . '.layout.php';

        if (file_exists($layoutFile)) {
            $slot = $content;
            $data = $data;
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
     * @param callable|array $handler Definición del controlador para la ruta.
     * @return Route La instancia de la ruta añadida.
     */
    public static function addRoute(string $method, string $uri, callable|array $handler): Route
    {
        $uri = trim($uri, '/');
        $route = new Route($handler);
        self::$routes[strtoupper($method)][$uri] = $route;
        return $route;
    }

    /** 
     * Añade una ruta GET.
     * 
     * @param string $uri URI de la ruta.
     * @param callable|array $handler Definición del controlador para la ruta.
     * @return Route La instancia de la ruta añadida.
     */
    public static function get(string $uri, callable|array $handler): Route
    {
        return self::addRoute('GET', $uri, $handler);
    }

    /** 
     * Añade una ruta POST. 
     * 
     * @param string $uri URI de la ruta.
     * @param callable|array $handler Definición del controlador para la ruta.
     * @return Route La instancia de la ruta añadida.
     */
    public static function post(string $uri, callable|array $handler): Route
    {
        return self::addRoute('POST', $uri, $handler);
    }

    /**
     * Añade una ruta PUT.
     * 
     * @param string $uri URI de la ruta.
     * @param callable|array $handler Definición del controlador para la ruta.
     * @return Route La instancia de la ruta añadida.
     */
    public static function put(string $uri, callable|array $handler): Route
    {
        return self::addRoute('PUT', $uri, $handler);
    }

    /** 
     * Añade una ruta DELETE. 
     * 
     * @param string $uri URI de la ruta.
     * @param callable|array $handler Definición del controlador para la ruta.
     * @return Route La instancia de la ruta añadida.
     */
    public static function delete(string $uri, callable|array $handler): Route
    {
        return self::addRoute('DELETE', $uri, $handler);
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
