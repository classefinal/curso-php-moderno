<?php

/**
 * @psalm-import-type Route from types
 */

/**
 * @var Route[] $routes
 */
$routes = [
    [
        'id' => 'home',
        'value' => '/',
        'include' => 'home',
        'isRegex' => false,
        'title' => 'Home',
        'call' => 'makeHome',
    ],
    [
        'id' => 'about',
        'value' => '/sobre',
        'include' => 'about',
        'isRegex' => false,
        'title' => 'Sobre',
        'call' => 'makeAbout',
    ],
    [
        'id' => 'products',
        'value' => '/products',
        'include' => 'products',
        'isRegex' => false,
        'title' => 'Produtos',
        'call' => 'makeProducts',
    ],
    [
        'id' => 'product',
        'value' => '/^\/product\/[a-z0-9]+$/',
        'include' => 'product',
        'isRegex' => true,
        'call' => 'makeProduct',
    ],
];