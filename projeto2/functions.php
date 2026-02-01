<?php

/**
 * @psalm-import-type Config from types
 */

function getComponentsPath(): string
{
    return 'components' . DIRECTORY_SEPARATOR;
}

/**
 * @param string $file
 * @param Config $data
 * @return void
 */
function getRequire(string $file, array $data = []): void
{
    extract($data);

    require_once COMPONENTS . "$file.php";
}

/**
 * @param Config $data
 * @return void
 */
function makePage(array $data): void
{
    getRequire('header', $data);

    getRequire('table', $data);

    getRequire('footer', $data);
}

function makeSearchParameter(string $searchUrl, string $productName): string
{
    return $searchUrl . '?' . http_build_query([
        'q' => $productName
    ]);
}
