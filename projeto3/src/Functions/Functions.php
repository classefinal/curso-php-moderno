<?php

/**
 * @psalm-import-type Route from Types
 */

declare(strict_types=1);

/**
 * @param Route $route
 * @return boolean
 */
function isMenuAllowed(array $route): bool
{
    if ((isset($_SESSION['admin']) || isset($_SESSION['user'])) && ($route['id'] === 'login' || $route['id'] === 'login_page')) {
        return false;
    }

    return !empty($route['inMenu']) && !empty($route['label']) && !empty($route['value']);
}

/**
 * @param Route $route
 * @param string|null $uri
 * @return boolean
 */
function isHomeRoute(array $route, ?string $uri): bool
{
    return (
        empty($uri) &&
        !empty($route['id']) &&
        $route['id'] === 'home'
    );
}

/**
 * @param Route $route
 * @param Route $currentRoute
 * @return boolean
 */
function isRouteInAllowedRoutes(array $route, array $currentRoute): bool
{
    return (
        !empty($route['allowedRoutes']) &&
        !empty($route['id']) &&
        !empty($currentRoute['id']) &&
        in_array($currentRoute['id'], $route['allowedRoutes'])
    );
}

/**
 * @param Route $route
 * @param string|null $uri
 * @param Route $currentRoute
 * @return boolean
 */
function isMenuActive(array $route, ?string $uri, array $currentRoute): bool
{
    return
        $route['value'] === $uri ||
        isHomeRoute($route, $uri) ||
        (!empty($route['allowedRoutes']) && !empty($route['id']) && !empty($currentRoute['id']) && in_array($currentRoute['id'], $route['allowedRoutes']));
}

/**
 * @param Route[] $routes
 * @param string|null $uri
 * @param Route $currentRoute
 * @return Route[]
 */
function getMenuItens(array $routes, ?string $uri, array $currentRoute): array
{
    $filteredRoutes = [];

    foreach ($routes as $route) {
        if (!isMenuAllowed($route)) {
            continue;
        }

        if (isMenuActive($route, $uri, $currentRoute)) {
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
