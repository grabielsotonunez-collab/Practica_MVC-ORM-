<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
    /**
     * @var array<int, array{
     *     method: string,
     *     path: string,
     *     handler: array{0: class-string, 1: string}
     * }>
     */
    private array $routes = [];

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function get(
        string $path,
        array $handler
    ): void {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function post(
        string $path,
        array $handler
    ): void {
        $this->add('POST', $path, $handler);
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function put(
        string $path,
        array $handler
    ): void {
        $this->add('PUT', $path, $handler);
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function delete(
        string $path,
        array $handler
    ): void {
        $this->add('DELETE', $path, $handler);
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    private function add(
        string $method,
        string $path,
        array $handler
    ): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(
        string $method,
        string $uri
    ): void {
        $method = strtoupper($method);

        /*
         * Los formularios HTML solamente admiten GET y POST.
         * _method permite representar PUT y DELETE.
         */
        $methodOverride = $_POST['_method'] ?? null;

        if (
            $method === 'POST'
            && is_string($methodOverride)
        ) {
            $methodOverride = strtoupper($methodOverride);

            if (in_array(
                $methodOverride,
                ['PUT', 'PATCH', 'DELETE'],
                true,
            )) {
                $method = $methodOverride;
            }
        }

        $path = parse_url($uri, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            $path = '/';
        }

        $path = $this->removeBasePath($path);
        $path = '/'.trim($path, '/');

        if ($path === '//') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->createPattern(
                $route['path']
            );

            if (! preg_match($pattern, $path, $matches)) {
                continue;
            }

            $parameters = [];

            foreach ($matches as $key => $value) {
                if (! is_string($key)) {
                    continue;
                }

                $parameters[] = ctype_digit($value)
                    ? (int) $value
                    : $value;
            }

            [$controllerClass, $action] =
                $route['handler'];

            if (! class_exists($controllerClass)) {
                throw new RuntimeException(
                    "No existe el controlador {$controllerClass}."
                );
            }

            $controller = new $controllerClass();

            if (! method_exists($controller, $action)) {
                throw new RuntimeException(
                    "No existe el método {$action}."
                );
            }

            $controller->{$action}(...$parameters);

            return;
        }

        http_response_code(404);

        echo '<h1>404</h1>';
        echo '<p>La página solicitada no existe.</p>';
    }

    private function removeBasePath(
        string $path
    ): string {
        $script = $_SERVER['SCRIPT_NAME']
            ?? '/index.php';

        $base = str_replace(
            '\\',
            '/',
            dirname($script),
        );

        if (
            $base !== '/'
            && $base !== '.'
            && str_starts_with($path, $base)
        ) {
            $path = substr($path, strlen($base));

            if ($path === '') {
                return '/';
            }
        }

        return $path;
    }

    private function createPattern(
        string $routePath
    ): string {
        $normalized = trim($routePath, '/');

        if ($normalized === '') {
            return '#^/?$#';
        }

        $segments = explode('/', $normalized);
        $patterns = [];

        foreach ($segments as $segment) {
            if (preg_match(
                '/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/',
                $segment,
                $matches,
            )) {
                $parameterName = $matches[1];

                $patterns[] =
                    '(?P<'.$parameterName.'>[^/]+)';

                continue;
            }

            $patterns[] = preg_quote($segment, '#');
        }

        return '#^/'.implode('/', $patterns).'/?$#';
    }
}