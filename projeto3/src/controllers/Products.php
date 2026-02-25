<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeProducts(array $configs, array $route, string $uri): void
{
    makePage('products', [
        'title' => 'Página de produtos',
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['response']();
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeProduct(array $configs, array $route, string $uri): void
{
    $routeItems = explode('/', $uri);

    $productId = array_last($routeItems);

    makePage('product', [
        'title' => "Página do produto com id - $productId",
        'productId' => $productId,
        'regex' => $route['value'],
        'controller' => $route['controller'],
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['response']();
}