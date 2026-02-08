<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

/**
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeProducts(array $route, string $uri): void
{
    makePage('products', [
        'title' => 'Página de produtos',
        'routes' => getMenuItens($uri),
    ]);
}

/**
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeProduct(array $route, string $uri): void
{
    $routeItems = explode('/', $uri);

    $productId = array_last($routeItems);

    makePage('product', [
        'title' => "Página do produto com id - $productId",
        'productId' => $productId,
        'regex' => $route['value'],
        'controller' => $route['controller'],
        'routes' => getMenuItens($uri),
    ]);
}
