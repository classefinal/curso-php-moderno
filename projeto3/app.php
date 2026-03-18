<?php

declare(strict_types=1);

ob_start();

define('SOURCES', 'src');
define('BASE_PATH', realpath(__DIR__));

// Initial config
require_once 'path.php';

// Constants of path
define('CONTROLLERS', getControllersPath());
define('COMPONENTS', getComponentsPath());
define('FUNCTIONS', getFunctionsPath());
define('PAGES', getPagesPath());
define('CONFIGS', getConfigsPath());
define('SERVICES', getServicesPath());
define('LISTENERS', getListenersPath());

// Requires
require_once FUNCTIONS . 'Functions.php';
require_once SERVICES . 'RouteResolver.php';
require_once SERVICES . 'Router.php';
require_once SERVICES . 'Environment.php';
require_once SERVICES . 'DB.php';
require_once SERVICES . 'Defer.php';
require_once SERVICES . 'Response.php';
require_once SERVICES . 'EventDispatcher.php';
require_once SERVICES . 'View.php';

$routes = require_once CONFIGS . 'routes.php';
$events = require_once CONFIGS . 'events.php';

loadEnv(BASE_PATH . DIRECTORY_SEPARATOR . '.env');

['dispatcher' => $dispatcher, 'defer' => $defer] = createDefer();

$connection = dbConnect();

$configs = [
    'routes' => $routes,
    'connection' => $connection,
    'defer' => $defer,
    'view' => createView(),
    ...createResponse($dispatcher),
];

createEventDispatcher($configs, $events);

processRoutes($configs);

ob_end_clean();

dbClose($connection);