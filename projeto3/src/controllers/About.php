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
function makeAbout(array $route, string $uri): void
{
    makePage('about', [
        'title' => 'Página sobre',
        'routes' => getMenuItens($uri),
    ]);
}
