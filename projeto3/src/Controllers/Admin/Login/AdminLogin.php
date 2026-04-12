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
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard', 302);

        return;
    }

    $content = $configs['view']('Admin/Login/login', [
        'title' => 'Login administrativo',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
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

    $content = $configs['view']('Admin/Login/login', [
        'title' => 'Login administrativo',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'error' => $error
    ]);

    $configs['response'](401, $content);
}

/**
 * @param Configs $configs
 * @return void
 */
function logoutAdminLogin(array $configs): void
{
    unset($_SESSION['admin']);

    $configs['redirect']('/', 303);
}
