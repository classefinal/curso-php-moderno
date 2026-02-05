<?php

declare(strict_types=1);

$uri = $_SERVER['REQUEST_URI'] ?? null;

// Requires
require_once 'routes.php';
require_once 'route_resolver.php';


if (empty($uri)) {
    makeHome();

    return;
}

$route = resolveRoute($uri, $routes);

if (!$route || empty($route['call']) || !function_exists($route['call'])) {
    makeNotFound();

    return;
}

$route['call']($route, $uri);