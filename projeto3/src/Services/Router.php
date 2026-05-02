<?php

declare(strict_types=1);

/**
 * @psalm-import-type Configs from types
 */

/**
 * @param string $controller
 * @return void
 */
function requireController(string $controller): void
{
    require_once CONTROLLERS . getRequirePath("$controller.php");
}

/**
 * @param string $middleware
 * @return void
 */
function requireMiddleware(string $middleware): void
{
    require_once MIDDLEWARES . getRequirePath("$middleware.php");
}

/**
 * @param array $middlewareStack
 * @param array $configs
 * @param array $route
 * @param string $uri
 * @param Closure $finalCallback
 * @return void
 */
function executeMiddlewares(
    array $middlewareStack,
    array &$configs,
    array $route,
    string $uri,
    Closure $finalCallback
): void {
    $next = function () use (&$middlewareStack, &$configs, $route, $uri, $finalCallback, &$next) {
        if (empty($middlewareStack)) {
            return $finalCallback();
        }

        $middlewareName = array_shift($middlewareStack);
        $middlewareFunction = $middlewareName . 'Middleware';

        requireMiddleware($middlewareName);

        return $middlewareFunction($configs, $route, $uri, $next);
    };

    $next();
}

/**
 * @param Configs $configs
 * @return void
 */
function processRoutes(array &$configs): void
{
    $uri = $_SERVER['REQUEST_URI'] ?? null;

    $defaultRoute = [
        'id' => 'home',
        'value' => '/',
        'controller' => 'Home',
        'call' => 'makeHome',
        'isRegex' => false,
        'middlewares' => [],
    ];

    $notFoundRoute = [
        'id' => 'notFound',
        'value' => '/NotFound',
        'controller' => 'NotFound',
        'call' => 'makeNotFound',
        'middlewares' => [],
    ];

    if (empty($uri)) {
        requireController($defaultRoute['controller']);

        executeMiddlewares(
            $defaultRoute['middlewares'],
            $configs,
            $defaultRoute,
            $uri,
            function () use ($defaultRoute, &$configs, $uri) {
                $defaultRoute['call']($configs, $defaultRoute, $uri);
            }
        );

        return;
    }

    $parsedUri = parse_url($uri, PHP_URL_PATH);
    $uri = $parsedUri === '/' ? $parsedUri : rtrim($parsedUri, "/");
    $route = resolveRoute($uri, $configs['routes']);

    if (!$route || empty($route['call'])) {
        requireController($notFoundRoute['controller']);

        executeMiddlewares(
            $notFoundRoute['middlewares'],
            $configs,
            $notFoundRoute,
            $uri,
            function () use ($notFoundRoute, &$configs, $uri) {
                $notFoundRoute['call']($configs, $notFoundRoute, $uri);
            }
        );

        return;
    }

    requireController($route['controller']);

    $middlewares = $route['middlewares'] ?? [];

    executeMiddlewares(
        $middlewares,
        $configs,
        $route,
        $uri,
        function () use ($route, &$configs, $uri) {
            $route['call']($configs, $route, $uri);
        }
    );
}
