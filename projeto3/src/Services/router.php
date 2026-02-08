<?php

$uri = $_SERVER['REQUEST_URI'] ?? null;

// Requires
require_once 'route_resolver.php';

/**
 * @param string $controller
 * @return void
 */
function requireController(string $controller): void
{
    require_once CONTROLLERS . $controller . '.php';
}


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
    requireController($defaultRoute['controller']);

    $defaultRoute['call']($defaultRoute, $uri);

    return;
}

$route = resolveRoute($uri, $routes);

if (!$route || empty($route['call']) || !function_exists($route['call'])) {
    requireController($notFoundRoute['controller']);

    $notFoundRoute['call']($notFoundRoute, $uri);

    return;
}

requireController($route['controller']);

$route['call']($route, $uri);
