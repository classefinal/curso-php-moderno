<?php

declare(strict_types=1);

require_once SERVICES . getRequirePath('Users/UsersService.php');

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
function viewProfile(array $configs, array $route, string $uri): void
{
    $user = $configs['user'];

    $content = $configs['view']('Users/profile', [
        'title' => "{$user['name']} - Perfil do usuário",
        'user' => $user,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['defer'](function () {
        if (!empty($_SESSION['profile_updated'])) {
            unset($_SESSION['profile_updated']);
        }
    });

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function updateProfile(array $configs, array $route, string $uri): void
{
    ['success' => $success, 'error' => $error, 'user' => $user] = updateUserProfile($configs['connection'], $configs['user']);

    if ($success) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }

    $content = $configs['view']('Users/profile', [
        'title' => "{$user['name']} - Perfil do usuário",
        'user' => $user,
        'error' => $error,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['response'](422, $content);
}
