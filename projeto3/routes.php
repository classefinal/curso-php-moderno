<?php

/**
 * @psalm-import-type Route from types
 */

require_once CONTROLLERS . '404.php';
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
        'isRegex' => false,
        'title' => 'Home',
        'controller' => 'Home',
        'call' => 'makeHome',
    ],
    [
        'id' => 'about',
        'value' => '/sobre',
        'isRegex' => false,
        'title' => 'Sobre',
        'controller' => 'About',
        'call' => 'makeAbout',
    ],
    [
        'id' => 'products',
        'value' => '/produtos',
        'isRegex' => false,
        'title' => 'Produtos',
        'controller' => 'Products',
        'call' => 'makeProducts',
    ],
    [
        'id' => 'product',
        'value' => '/^\/produtos\/[a-z0-9]+$/',
        'isRegex' => true,
        'controller' => 'Products',
        'call' => 'makeProduct',
    ],
];
