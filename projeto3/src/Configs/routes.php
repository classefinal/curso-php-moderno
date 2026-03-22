<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

/**
 * @return Route[]
 */
return [
    [
        'id' => 'home',
        'value' => '/',
        'controller' => 'Home',
        'call' => 'makeHome',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Home',
        'order' => 0,
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
        'controller' => 'Products/Products',
        'call' => 'makeProducts',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Produtos',
        'order' => 1,
        'allowedRoutes' => ['product']
    ],
    [
        'id' => 'product',
        'value' => '/^\/produtos\/[a-zA-Z0-9]+$/',
        'controller' => 'Products/Products',
        'call' => 'makeProduct',
        'isRegex' => true
    ],
];
