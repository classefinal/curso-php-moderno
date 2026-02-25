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

// Requires
require_once FUNCTIONS . 'functions.php';
require_once CONFIGS . 'routes.php';
require_once SERVICES . 'route_resolver.php';
require_once SERVICES . 'router.php';
require_once SERVICES . 'environment.php';
require_once SERVICES . 'db.php';
require_once SERVICES . 'defer.php';
require_once SERVICES . 'response.php';

loadEnv(BASE_PATH . DIRECTORY_SEPARATOR . '.env');

['dispatcher' => $dispatcher, 'defer' => $defer] = createDefer();

$connection = dbConnect();

$configs = [
    'routes' => getRoutes(),
    'connection' => $connection,
    'defer' => $defer,
    'response' => createResponse($dispatcher),
];

processRoutes($configs);

ob_end_clean();

dbClose($connection);