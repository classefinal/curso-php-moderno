<?php

/**
 * @psalm-import-type Route from types
 */

/**
 * @param string $uri
 * @param Route[] $routes
 * @return ?Route
 */
function resolveRoute(string $uri, array $routes): ?array
{
    foreach ($routes as $route) {
        if (empty($route['value']) || empty($route['controller'])) {
            continue;
        }

        if (empty($route['isRegex']) && $uri === $route['value']) {
            return $route;
        }

        if ($route['isRegex'] && preg_match($route['value'], $uri)) {
            return $route;
        }
    }

    return null;
}
