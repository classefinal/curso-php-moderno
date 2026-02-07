<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

require_once CONTROLLERS . 'NotFound.php';
require_once CONTROLLERS . 'About.php';
require_once CONTROLLERS . 'Home.php';
require_once CONTROLLERS . 'Products.php';

/**
 * @var Route[] $routes
 */
$routes = [
    [
        'id' => 'home',
        'value' => '/',
        'controller' => 'Home',
        'call' => 'makeHome',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Home',
        'order' => 0
    ],
    [
        'id' => 'about',
        'value' => '/sobre',
        'controller' => 'About',
        'call' => 'makeAbout',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Sobre',
        'order' => 2
    ],
    [
        'id' => 'products',
        'value' => '/produtos',
        'controller' => 'Products',
        'call' => 'makeProducts',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Produtos',
        'order' => 1
    ],
    [
        'id' => 'product',
        'value' => '/^\/produtos\/[a-zA-Z0-9]+$/',
        'controller' => 'Products',
        'call' => 'makeProduct',
        'isRegex' => true
    ],
];

$_GLOBALS['routes'] = $routes;
