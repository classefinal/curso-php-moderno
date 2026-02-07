<?php

$uri = $_SERVER['REQUEST_URI'] ?? null;

// Requires
require_once 'route_resolver.php';

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
    $defaultRoute['call']($defaultRoute, $uri);

    return;
}

$route = resolveRoute($uri, $routes);

if (!$route || empty($route['call']) || !function_exists($route['call'])) {
    makeNotFound($notFoundRoute, $uri);

    return;
}

$route['call']($route, $uri);
