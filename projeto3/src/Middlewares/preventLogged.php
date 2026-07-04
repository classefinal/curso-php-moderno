<?php

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
function preventLoggedMiddleware(array &$configs, array $route, string $uri, Closure $next)
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
