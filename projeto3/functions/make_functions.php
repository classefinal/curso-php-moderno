<?php

/**
 * @psalm-import-type Route from types
 */

function makePage(string $include, array $args): void
{
    extract($args);

    require COMPONENTS . 'header.php';

    require PAGES . $include;

    require COMPONENTS . 'footer.php';
}

function make404(): void
{
    $title = 'Página não encontrada';

    makePage('404.php', [
        'title' => $title
    ]);
}

function makeHome(): void
{
    $title = 'Home';

    makePage('home.php', [
        'title' => $title
    ]);
}

/**
 * @param Route $route
 * @return void
 */
function makeAbout(array $route): void
{
    $title = 'Sobre';

    makePage('about.php', [
        'title' => $title
    ]);
}

/**
 * @param Route $route
 * @return void
 */
function makeProducts(array $route): void
{
    $title = 'Produtos';

    makePage('products.php', [
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

    makePage('product.php', [
        'title' => $title,
        'productId' => $productId,
        'originalRegex' => $route['value'],
    ]);
}
