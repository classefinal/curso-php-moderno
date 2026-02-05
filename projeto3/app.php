<?php

declare(strict_types=1);

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

// Requires
require_once FUNCTIONS . 'functions.php';
require_once CONFIGS . 'router.php';