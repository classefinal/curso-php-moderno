<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

/**
 * Middleware de autenticação simples
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @param Closure $next
 * @return mixed
 */
function authMiddleware(array $configs, array $route, string $uri, Closure $next)
{
    $configs['redirect']('/logout', 303);

    $next();
}
