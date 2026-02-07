<?php

declare(strict_types=1);

// Requires
require_once 'route_resolver.php';

/**
 * @psalm-import-type Configs from types
 */

/**
 * @param Configs $configs
 * @return void
 */
function processRoutes(array $configs): void
{
    $uri = $_SERVER['REQUEST_URI'] ?? null;

    $defaultRoute = [
        'id' => 'default',
        'controller' => 'Home',
        'call' => 'makeHome',
        'isRegex' => false,
    ];

    $notFoundRoute = [
        'id' => 'notFound',
        'controller' => 'NotFound',
        'call' => 'makeNotFound',
        'isRegex' => false,
    ];

    if (empty($uri)) {
        $defaultRoute['call']($configs, $defaultRoute, $uri);

        return;
    }

    $route = resolveRoute($uri, $configs['routes']);

    if (!$route || empty($route['call']) || !function_exists($route['call'])) {
        makeNotFound($configs, $notFoundRoute, $uri);

        return;
    }

    $route['call']($configs, $route, $uri);
}
