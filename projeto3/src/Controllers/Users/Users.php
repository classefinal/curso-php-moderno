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
    if (!isset($_SESSION['user']['id']) || empty($_SESSION['user']['active'])) {
        $configs['redirect']('/logout', 303);

        return;
    }

    $user = getUserById($configs['connection'], $_SESSION['user']['id']);

    if (!$user) {
        $configs['redirect']('/logout', 303);

        return;
    }

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
    if (!isset($_SESSION['user']['id']) || empty($_SESSION['user']['active'])) {
        $configs['redirect']('/logout', 303);

        return;
    }

    ['success' => $success, 'error' => $error, 'user' => $user] = updateUserProfile($configs['connection'], $_SESSION['user']['id']);

    if (!$user) {
        $configs['redirect']('/logout', 303);

        return;
    }

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
