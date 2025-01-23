<?php

namespace Core\Routing;

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
        $uri = self::getCurrentRoute();

        try {
            self::handleRequest($method, $uri);
        } catch (Exception $e) {
            self::handleError($e);
        }
    }

    /**
     * Maneja la solicitud HTTP entrante
     */
    private static function handleRequest(string $method, string $uri): void
    {
        foreach (self::$routes[$method] as $route => $routeObject) {
            $params = self::matchRoute($route, $uri);

            if ($params !== null) {
                self::executeRoute($routeObject, $params);
                return;
            }
        }

        self::handle404();
    }

    /**
     * Ejecuta la ruta encontrada con sus middlewares y controladores
     */
    private static function executeRoute(Route $route, array $params): void
    {
        self::executeMiddleware($route);
        $content = self::executeHandler($route->handler, $params);
        self::renderResponse($route, $content);
    }

    /**
     * Ejecuta el middleware asociado a la ruta si existe
     */
    private static function executeMiddleware(Route $route): void
    {
        if ($middleware = $route->getMiddleware()) {
            $middlewareInstance = new $middleware();
            $middlewareInstance->handle();
        }
    }

    /**
     * Ejecuta el controlador de la ruta y obtiene el contenido
     */
    private static function executeHandler(callable|array $handler, array $params): string
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        [$controllerClass, $controllerMethod] = $handler;

        if (!method_exists($controllerClass, $controllerMethod)) {
            throw new InvalidArgumentException(
                "El método $controllerMethod no existe en el controlador $controllerClass"
            );
        }

        return call_user_func_array([$controllerClass, $controllerMethod], $params);
    }

    /**
     * Renderiza la respuesta con el layout apropiado
     */
    private static function renderResponse(Route $route, string $content): void
    {
        if ($layout = $route->layout) {
            self::renderLayout($layout, $content, $route->layoutData ?? []);
        } else {
            print($content);
        }
    }

    /**
     * Maneja el caso cuando no se encuentra la ruta
     */
    private static function handle404(): void
    {
        if (self::$notFoundCallback) {
            call_user_func(self::$notFoundCallback);
        } else {
            http_response_code(404);
            print("404 Not Found");
        }
    }

    /**
     * Maneja errores durante el procesamiento de la solicitud
     */
    private static function handleError(Exception $e): void
    {
        // Aquí podríamos implementar un manejo más sofisticado de errores
        http_response_code(500);
        print("Error interno del servidor: " . $e->getMessage());
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
     * @param string $method Método HTTP válido ('GET', 'POST', 'PUT', 'DELETE')
     * @param string $uri URI válida que comienza con '/'
     * @param callable|array{0: class-string, 1: string} $handler Controlador de la ruta
     * @throws RouterException Si los parámetros no son válidos
     */
    public static function addRoute(string $method, string $uri, callable|array $handler): Route
    {
        $uri = trim($uri, '/');
        $route = new Route($handler);

        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new Exception("Método HTTP no soportado: $method");
        }

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

    /**
     * Verifica si la ruta actual coincide con la ruta o rutas especificadas
     *
     * @param string|array $routes Una ruta o array de rutas a verificar
     * @return bool True si la ruta actual coincide con alguna de las especificadas
     */
    public static function isCurrentRoute(string|array $routes): bool
    {
        $currentPath = self::getCurrentRoute();
        $currentMethod = $_SERVER['REQUEST_METHOD'];
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            $routePattern = trim($route, '/');
            $routeExists = isset(self::$routes[$currentMethod][$routePattern]);

            if ($routeExists) {
                $params = self::matchRoute($routePattern, $currentPath);

                if ($params !== null) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Obtiene la ruta actual de la aplicación
     *
     * @return string La ruta actual normalizada
     */
    public static function getCurrentRoute(): string
    {
        return trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
    }

    /**
     * Comprueba si una ruta coincide con un patrón y extrae sus parámetros
     *
     * @param string $pattern El patrón de ruta a comprobar
     * @param string $path La ruta actual
     * @return array|null Retorna un array con los parámetros si hay coincidencia, null si no coincide
     */
    private static function matchRoute(string $pattern, string $path): ?array
    {
        $trimmedPattern = trim($pattern, '/');
        $trimmedPath = trim($path, '/');

        if ($trimmedPattern === $trimmedPath) {
            return [];
        }

        // Convertimos el patrón de ruta en una expresión regular
        $regexPattern = preg_replace('/:[^\/]+/', '([^/]+)', $pattern);

        // Si hay coincidencia, preg_match llenará $matches con los grupos capturados
        if (preg_match("#^$regexPattern$#", $path, $matches)) {
            array_shift($matches);
            return $matches;
        }

        return null;
    }
}
