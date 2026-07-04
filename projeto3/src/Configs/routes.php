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
        'methods' => ['GET'],
        'middlewares' => []
    ],
    [
        'id' => 'about',
        'value' => '/sobre',
        'controller' => 'About',
        'call' => 'makeAbout',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Sobre',
        'order' => 2,
        'methods' => ['GET'],
        'middlewares' => []
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
        'allowedRoutes' => ['product'],
        'methods' => ['GET'],
        'middlewares' => []
    ],
    [
        'id' => 'product',
        'value' => '/^\/produtos\/[a-zA-Z0-9]+$/',
        'controller' => 'Products/Products',
        'call' => 'makeProduct',
        'isRegex' => true,
        'methods' => ['GET'],
        'middlewares' => []
    ],
    [
        'id' => 'admin_login_page',
        'value' => '/admin/login',
        'controller' => 'Admin/Login/AdminLogin',
        'call' => 'makeAdminLogin',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Login admin',
        'order' => 999,
        'methods' => ['GET'],
        'middlewares' => ['preventLogged']
    ],
    [
        'id' => 'admin_login',
        'value' => '/admin/login',
        'controller' => 'Admin/Login/AdminLogin',
        'call' => 'validateAdminLogin',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Login admin',
        'order' => 999,
        'methods' => ['POST'],
        'middlewares' => ['preventLogged']
    ],
    [
        'id' => 'admin_logout',
        'value' => '/admin/logout',
        'controller' => 'Admin/Login/AdminLogin',
        'call' => 'logoutAdminLogin',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Logout admin',
        'order' => 999,
        'methods' => ['GET'],
        'middlewares' => []
    ],
    [
        'id' => 'login_page',
        'value' => '/login',
        'controller' => 'Login/Login',
        'call' => 'makeLogin',
        'isRegex' => false,
        'inMenu' => true,
        'label' => 'Login',
        'order' => 999,
        'methods' => ['GET'],
        'middlewares' => ['preventLogged']
    ],
    [
        'id' => 'login',
        'value' => '/login',
        'controller' => 'Login/Login',
        'call' => 'validateLogin',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Login',
        'order' => 999,
        'methods' => ['POST'],
        'middlewares' => ['preventLogged']
    ],
    [
        'id' => 'logout',
        'value' => '/logout',
        'controller' => 'Login/Login',
        'call' => 'logoutLogin',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Logout',
        'order' => 999,
        'methods' => ['GET'],
        'middlewares' => []
    ],
    [
        'id' => 'user_profile',
        'value' => '/usuario/perfil',
        'controller' => 'Users/Users',
        'call' => 'viewProfile',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Perfil',
        'order' => 999,
        'methods' => ['GET'],
        'middlewares' => ['auth']
    ],
    [
        'id' => 'user_profile_update',
        'value' => '/usuario/perfil',
        'controller' => 'Users/Users',
        'call' => 'updateProfile',
        'isRegex' => false,
        'inMenu' => false,
        'label' => 'Perfil',
        'order' => 999,
        'methods' => ['POST'],
        'middlewares' => ['auth']
    ],
];
