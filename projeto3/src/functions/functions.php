<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

function makePage(string $page, array $args): void
{
    extract($args);

    require_once COMPONENTS . 'header.php';

    require_once PAGES . $page . '.php';

    require_once COMPONENTS . 'footer.php';
}

/**
 * @param Route $route
 * @param string|null $uri
 * @return boolean
 */
function isMenuActive(array $route, ?string $uri): bool
{
    return (
        empty($uri) &&
        !empty($route['id']) &&
        $route['id'] === 'home'
    ) || (
        !empty($route['value']) &&
        !empty($uri) && $route['value'] === $uri
    );
}

function getMenuItens(?string $uri): array
{
    /** @var Route[] $routes */
    $routes = $GLOBALS['routes'];

    $filteredRoutes = array_filter(
        $routes,
        fn(array $route) => !empty($route['inMenu']) && !empty($route['label'])
    );

    foreach ($filteredRoutes as &$route) {
        if (isMenuActive($route, $uri)) {
            $route['active'] = true;

            break;
        }
    }

    usort($filteredRoutes, fn(array $a, array $b) => $a['order'] <=> $b['order']);

    return $filteredRoutes;
}
