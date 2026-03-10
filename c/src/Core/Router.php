<?php
declare(strict_types=1);

namespace Staff\Core;

/**
 * Router minimaliste
 * Mappe les URL vers des contrôleurs/méthodes
 */
class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Retire le préfixe /staff/public
        $uri = preg_replace('#^/staff/public#', '', $uri) ?: '/';

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex   = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($handler, $params);
                return;
            }
        }

        http_response_code(404);
        require BASE_PATH . '/src/Views/errors/404.php';
    }

    private function call(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $obj = new $class();
            $obj->$method(...array_values($params));
        } else {
            $handler(...array_values($params));
        }
    }
}
