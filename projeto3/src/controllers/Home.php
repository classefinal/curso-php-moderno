<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 */

/**
 * @param Route $route
 * @param ?string $uri
 * @return void
 */
function makeHome(array $route, ?string $uri): void
{
    makePage('home', [
        'title' => 'Página inicial',
        'routes' => getMenuItens($uri),
        'uri' => $uri
    ]);
}
