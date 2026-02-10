<?php

declare(strict_types=1);

function requireController(string $controller): void
{
    require_once CONTROLLERS . "$controller.php";
}

$uri = $_SERVER['REQUEST_URI'] ?? null;

$defaultRoute = [
    'id' => 'home',
    'value' => '/',
    'controller' => 'Home',
    'call' => 'makeHome',
    'isRegex' => false,
];

$notFoundRoute = [
    'id' => 'notFound',
    'value' => '/NotFound',
    'controller' => 'NotFound',
    'call' => 'makeNotFound',
];

if (empty($uri)) {
    requireController($defaultRoute['controller']);

    $defaultRoute['call']($defaultRoute, $uri);

    return;
}

$uri = rtrim(parse_url($uri, PHP_URL_PATH), "/");

$route = resolveRoute($uri, $routes);

if (!$route || empty($route['call']) || !function_exists($route['call'])) {
    requireController($notFoundRoute['controller']);

    $notFoundRoute['call']($notFoundRoute, $uri);

    return;
}

requireController($route['controller']);

$route['call']($route, $uri);
