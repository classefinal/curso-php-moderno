<?php

/**
 * @psalm-import-type Route from types
 */

declare(strict_types=1);

/**
 * @param Route $route
 * @return boolean
 */
function isMenuAllowed(array $route): bool
{
    return !empty($route['inMenu']) && !empty($route['label']) && !empty($route['value']);
}

/**
 * @param Route $route
 * @param string|null $uri
 * @return boolean
 */
function isMenuActive(array $route, ?string $uri): bool {
    return $route['value'] === $uri || (empty($uri) && !empty($route['id']) && $route['id'] === 'home');
}

/**
 * @param Route[] $routes
 * @param string|null $uri
 * @return Route[]
 */
function getMenuItens(array $routes, ?string $uri): array
{
    $filteredRoutes = [];

    foreach ($routes as $route) {
        if(!isMenuAllowed($route)) {
            continue;
        }

        if (isMenuActive($route, $uri)) {
            $route['active'] = true;
        }

        $filteredRoutes[] = $route;
    }

    usort(
        $filteredRoutes,
        fn(array $routeA, array $routeB) => $routeA['order'] <=> $routeB['order']
    );

    return $filteredRoutes;
}
