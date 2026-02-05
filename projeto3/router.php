<?php

$uri = $_SERVER['PATH_INFO'] ?? null;

// Initial config
require_once 'path.php';

// Constants of path
define('CONTROLLLERS', getControllersPath());
define('COMPONENTS', getComponentsPath());
define('FUNCTIONS', getFunctionsPath());
define('PAGES', getPagesPath());

// Requires
require_once 'routes.php';
require_once 'route_resolver.php';
require_once FUNCTIONS . 'functions.php';


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