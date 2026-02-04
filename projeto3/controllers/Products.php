<?php

/**
 * @psalm-import-type Route from types
 */

/**
 * @return void
 */
function makeProducts(): void
{
    $title = 'Produtos';

    makePage('products', [
        'title' => $title
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

    $title = 'Produto - ' . $productId;

    makePage('product', [
        'title' => $title,
        'productId' => $productId,
        'originalRegex' => $route['value'],
    ]);
}