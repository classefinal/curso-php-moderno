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
function makeAbout(array $configs, array $route, string $uri): void
{
    $content = $configs['view']('about', [
        'title' => 'Página sobre',
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['response'](content: $content);
}
