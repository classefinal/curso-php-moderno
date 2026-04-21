<?php

declare(strict_types=1);

require_once SERVICES . getRequirePath('Login/LoginService.php');

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeLogin(array $configs, array $route, string $uri): void
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard', 302);

        return;
    }

    if (isset($_SESSION['user'])) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }

    $content = $configs['view']('Login/login', [
        'title' => 'Login',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'action' => '/login'
    ]);

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function validateLogin(array $configs, array $route, string $uri): void
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard', 302);

        return;
    }

    if (isset($_SESSION['user'])) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }
    
    ['success' => $success, 'error' => $error] = loginAuthenticate($configs['connection'], $configs['eventDispatcher']);

    if ($success) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }

    $content = $configs['view']('Login/login', [
        'title' => 'Login',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'error' => $error,
        'action' => '/login'
    ]);

    $configs['response'](401, $content);
}


/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function logoutLogin(array $configs, array $route, string $uri): void
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/logout', 303);

        return;
    }

    unset($_SESSION['user']);

    $configs['redirect']('/', 303);
}
