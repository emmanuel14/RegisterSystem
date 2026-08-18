<?php

/**
 * Router – Simple, fast regex-based router.
 */
class Router
{
    private array $routes = [];

    /** Register a GET route. */
    public function get(string $pattern, string $controller, string $method): void
    {
        $this->routes[] = ['GET', $pattern, $controller, $method];
    }

    /** Register a POST route. */
    public function post(string $pattern, string $controller, string $method): void
    {
        $this->routes[] = ['POST', $pattern, $controller, $method];
    }

    /** Register both GET and POST. */
    public function any(string $pattern, string $controller, string $method): void
    {
        $this->get($pattern, $controller, $method);
        $this->post($pattern, $controller, $method);
    }

    /** Dispatch the current request. */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the subdirectory prefix so the router works whether the app
        // is at / (virtual host) OR at /ems/public/ (XAMPP subfolder).
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Normalize front-controller entry points such as /index.php or /.
        $uri = preg_replace('#/index\.php/?$#', '/', $uri);
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';w
        }

        foreach ($this->routes as [$routeMethod, $pattern, $controllerClass, $action]) {
            if ($method !== $routeMethod) continue;

            $regex = preg_replace('/\{([a-z_]+):int\}/', '([0-9]+)', $pattern);
            $regex = preg_replace('/\{([a-z_]+)\}/', '([^/]+)', $regex);
            $regex = '@^' . $regex . '$@';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // Remove full match

                $fqClass = 'Controllers\\' . $controllerClass;
                if (!class_exists($fqClass)) {
                    http_response_code(500);
                    die("Controller not found: {$fqClass}");
                }

                $controller = new $fqClass();
                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    die("Action not found: {$action}");
                }

                // Type-cast integer params
                $args = array_map(fn($v) => ctype_digit($v) ? (int)$v : $v, $matches);
                call_user_func_array([$controller, $action], $args);
                return;
            }
        }

        // 404
        http_response_code(404);
        require VIEW_PATH . '/public/404.php';
    }
}
