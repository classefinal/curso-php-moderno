<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

function makeProducts(): void
{
    makePage('products', [
        'title' => 'Página de produtos'
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
    ]);
}