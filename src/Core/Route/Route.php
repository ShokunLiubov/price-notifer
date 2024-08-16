<?php

declare(strict_types=1);

namespace App\Core\Route;

use Exception;

class Route
{
    private static array $routes = [];

    public static function get($path, $controllerAction, $middleware = []): void
    {
        self::add('GET', $path, $controllerAction, $middleware);
    }

    public static function post($path, $controllerAction, $middleware = []): void
    {
        self::add('POST', $path, $controllerAction, $middleware);
    }

    public static function put($path, $controllerAction, $middleware = []): void
    {
        self::add('PUT', $path, $controllerAction, $middleware);
    }

    public static function patch($path, $controllerAction, $middleware = []): void
    {
        self::add('PATCH', $path, $controllerAction, $middleware);
    }

    public static function delete($path, $controllerAction, $middleware = []): void
    {
        self::add('DELETE', $path, $controllerAction, $middleware);
    }

    private static function add($method, $path, $controllerAction, $middleware): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controllerAction[0],
            'action' => $controllerAction[1],
        ];
    }

    public static function dispatch($method, $path): void
    {
        try {
            foreach (self::$routes as $route) {
                $pattern = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9]+)', $route['path']);
                $pattern = "@^" . $_ENV['BASE_URL'] . $pattern . "\/?$@D";

                if ($method === $route['method'] && preg_match($pattern, $path, $getParams)) {
                    array_shift($getParams);
                    $controller = new $route['controller']();
                    $action = $route['action'];

                    call_user_func_array([$controller, $action], $getParams);

                    return;
                }
            }

            throw new Exception('Page not found');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
