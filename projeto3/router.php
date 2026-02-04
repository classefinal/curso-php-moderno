<?php

$uri = $_SERVER['PATH_INFO'] ?? null;

// Initial config
require_once 'path.php';

// Constants of paths
define('CONTROLLERS', getControllersPath());
define('COMPONENTS', getComponentsPath());
define('FUNCTIONS', getFunctionsPath());
define('PAGES', getPagesPath());

// Requires
require_once 'routes.php';
require_once 'route_resolver.php';
require_once FUNCTIONS . 'functions.php';

if (empty($uri) || $uri === '/') {
    makeHome();

    return;
}

$route = resolveRoute($uri, $routes);

if (!$route || empty($route['call'])) {
    make404();

    return;
}

$route['call']($route, $uri);
