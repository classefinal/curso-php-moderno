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
function makeAdminLogin(array $configs, array $route, string $uri): void
{
    $content = $configs['view']('Login/login', [
        'title' => 'Login administrativo',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'action' => '/admin/login'
    ]);

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function validateAdminLogin(array $configs, array $route, string $uri): void
{
    ['success' => $success, 'error' => $error] = adminLoginAuthenticate($configs['connection'], $configs['eventDispatcher']);

    if ($success) {
        $configs['redirect']('/admin/dashboard', 302);

        return;
    }

    $content = $configs['view']('Login/login', [
        'title' => 'Login administrativo',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'error' => $error,
        'action' => '/admin/login'
    ]);

    $configs['response'](401, $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function logoutAdminLogin(array $configs, array $route, string $uri): void
{
    if (isset($_SESSION['user'])) {
        $configs['redirect']('/logout', 303);

        return;
    }

    unset($_SESSION['admin']);

    $configs['redirect']('/', 303);
}
