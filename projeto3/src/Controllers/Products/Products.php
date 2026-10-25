<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
 */

require_once SERVICES . getRequirePath('Products/ProductsService.php');
require_once SERVICES . getRequirePath('Products/RandomProductsService.php');
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
        'title' => $activeCategory ? "Produtos - {$activeCategory['name']}" : 'Produtos',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'limit' => $limit,
        'products' => $products,
        'categories' => $categories,
        'categoryId' => $categoryId,
        'activeCategory' => $activeCategory,
        'randomProducts' => getRandomActiveProducts($configs['connection']),
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
    $product = getProductById($configs['connection'], $uri);

    if (is_null($product)) {
        $configs['response'](404, 'not found');

        return;
    }

    $content = $configs['view']('Products/product', [
        'title' => $product['name'],
        'product' => $product,
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'randomProducts' => getRandomActiveProducts($configs['connection']),
    ]);

    $configs['response'](content: $content);
}
