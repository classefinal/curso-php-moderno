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
 * @param ?string $uri
 * @return void
 */
function makeAdminLogin(array $configs, array $route, ?string $uri): void
{    
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard');

        return;
    }  

    $content = $configs['view']('Admin/Login/login', [
        'title' => 'Login Administrativo',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param ?string $uri
 * @return void
 */
function validateAdminLogin(array $configs, array $route, ?string $uri): void
{
    ['success' => $success, 'error' => $error] = adminLoginAuthenticate($configs['connection']);
    
    if ($success) {
        $configs['redirect']('/admin/dashboard');

        return;
    }  

    $content = $configs['view']('Admin/Login/login', [
        'title' => 'Login Administrativo',
        'error' => $error,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param ?string $uri
 * @return void
 */
function logoutAdminLogin(array $configs, array $route, ?string $uri): void
{
    unset($_SESSION['admin']);

    $configs['redirect']('/');
}
