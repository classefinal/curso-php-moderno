<?php

declare(strict_types=1);

require_once SERVICES . getRequirePath('Users/UsersService.php');

/**
 * @psalm-import-type Configs from types
 * @psalm-import-type Route from types
 */

/**
 * @param Configs &$configs
 * @param Route $route
 * @param string $uri
 * @param Closure $next
 * @return void
 */
function authMiddleware(array &$configs, array $route, string $uri, Closure $next)
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

    $configs['user'] = $user;

    $next();
}
