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
function makeNotFound(array $route, string $uri): void
{
    makePage('not_found', [
        'title' => 'Página não encontrada',
        'routes' => getMenuItens($uri),
    ]);
}
