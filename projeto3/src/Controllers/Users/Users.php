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
 * @param string|null $uri
 * @return void
 */
function viewProfile(array $configs, array $route, ?string $uri): void
{
    if (!isset($_SESSION['user']['id'])) {
        $configs['redirect']('/login', 307);

        return;
    }

    $user = getUserById($configs['connection'], $_SESSION['user']['id']);

    if (!$user) {
        $configs['redirect']('/logout', 307);

        return;
    }

    $content = $configs['view']('Users/profile', [
        'title' => $user['name'] . ' - Perfil do Usuário',
        'user' => $user,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['defer'](function(): void {
        if (isset($_SESSION['profile_updated'])) {
            unset($_SESSION['profile_updated']);
        }
    });

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string|null $uri
 * @return void
 */
function updateProfile(array $configs, array $route, ?string $uri): void
{
    if (!isset($_SESSION['user']['id'])) {
        $configs['redirect']('/login', 307);

        return;
    }

    ['success' => $success, 'error' => $error, 'user' => $user] = updateUserProfile($configs['connection'], $_SESSION['user']['id']);

    if (!$user) {
        $configs['redirect']('/logout', 307);

        return;
    }

    if ($success) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }

    $content = $configs['view']('Users/profile', [
        'title' => $user['name'] . ' - Perfil do Usuário',
        'user' => $user,
        'error' => $error,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['response'](content: $content);
}
