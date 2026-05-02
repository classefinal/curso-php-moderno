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
 * @param Closure $next
 * @return mixed
 */
function preventLoggedMiddleware(array $configs, array $route, string $uri, Closure $next)
{
    if (isset($_SESSION['admin'])) {
        $configs['redirect']('/admin/dashboard', 302);

        return;
    }

    if (isset($_SESSION['user'])) {
        $configs['redirect']('/usuario/perfil', 302);

        return;
    }
    
    $next();
}
