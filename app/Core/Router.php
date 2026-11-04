<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute("GET", $path, $handler, $middlewares);
    }

    public function post(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute("POST", $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array $handler, array $middlewares): void
    {
        // Convertir rutas con parametros como {id} o {code} a regex
        $pattern = preg_replace("/\{([a-zA-Z0-9_]+)\}/", "(?P<$1>[^/]+)", $path);
        $pattern = "#^" . $pattern . "$#";

        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "pattern" => $pattern,
            "controller" => $handler[0],
            "action" => $handler[1],
            "middlewares" => $middlewares,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route["method"] !== $method) {
                continue;
            }

            if (preg_match($route["pattern"], $uri, $matches)) {
                // Ejecutar middlewares asignados a la ruta
                foreach ($route["middlewares"] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (!$middleware->handle($request)) {
                        return;
                    }
                }

                // Filtrar parametros nombrados
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                $controllerClass = $route["controller"];
                $action = $route["action"];

                $controller = new $controllerClass();
                $controller->$action($request, $params);
                return;
            }
        }

        // Si no coincide ninguna ruta
        Response::notFound("La ruta " . htmlspecialchars($uri) . " no fue encontrada.");
    }
}

