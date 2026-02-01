<?php
/**
 * @psalm-import-type Product from types
 */

function getComponentsPath(): string
{
    return 'components' . DIRECTORY_SEPARATOR;
}

function includeHeader(string $title): void
{
    require_once getComponentsPath() . 'header.php';
}

function includeFooter(): void
{
    require_once getComponentsPath() . 'footer.php';
}

function makeSearch(string $baseUrl, string $productName): string
{
    return $baseUrl . '?' . http_build_query([
        'q' => $productName
    ]);
}

/**
 * @param Product[] $products
 * @param string $baseUrl
 * @return void
 */
function includeTable(array $products, string $baseUrl): void
{
    require_once getComponentsPath() . 'table.php';
}

/**
 * @param array{title: string, baseUrl: string, products: Product[]} $data
 * @return void
 */
function makePage(array $data): void
{
    includeHeader($data['title']);

    includeTable($data['products'], $data['baseUrl']);

    includeFooter();
}
