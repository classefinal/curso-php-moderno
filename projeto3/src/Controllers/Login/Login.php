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
function makeLogin(array $configs, array $route, ?string $uri): void
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard');

        return;
    }

    if (isset($_SESSION['user'])) {
        $configs['redirect']('/');

        return;
    }

    $content = $configs['view']('Login/login', [
        'title' => 'Login',
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
function validateLogin(array $configs, array $route, ?string $uri): void
{
    ['success' => $success, 'error' => $error] = loginAuthenticate($configs['connection']);

    if ($success) {
        $configs['redirect']('/');
        return;
    }

    $content = $configs['view']('Login/login', [
        'title' => 'Login',
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
function logoutLogin(array $configs, array $route, ?string $uri): void
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/logout');

        return;
    }

    unset($_SESSION['user']);

    $configs['redirect']('/');
}
