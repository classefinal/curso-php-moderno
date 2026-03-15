<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

require_once SERVICES . getRequirePath('Products/ProductsService.php');
require_once SERVICES . getRequirePath('Categories/CategoriesService.php');

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeProducts(array $configs, array $route, string $uri): void
{
    ['limit' => $limit, 'products' => $products, 'categoryId' => $categoryId] = getActiveProducts($configs['connection']);
    $categories = getActiveCategories($configs['connection']);

    /** @var ?Category $activeCategory */
    $activeCategory = null;

    if ($categoryId) {
        $activeCategory = getActiveCategoryById($configs['connection'], $categoryId);
    }

    $content = $configs['view']('Products/products', [
        'title' => $activeCategory ? "Produtos - {$activeCategory['name']}": 'Produtos',
        'routes' => getMenuItens($configs['routes'], $uri),
        'limit' => $limit,
        'products' => $products,
        'categories' => $categories,
        'categoryId' => $categoryId,
        'activeCategory' => $activeCategory,
    ]);

    $configs['response'](content: $content);
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

    $content = $configs['view']('Products/product', [
        'title' => "Página do produto com id - $productId",
        'productId' => $productId,
        'regex' => $route['value'],
        'controller' => $route['controller'],
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['response'](content: $content);
}
