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
function makeNotFound(array $configs, array $route, string $uri): void
{
    $content = $configs['view']('not_found', [
        'title' => 'Página não encontrada',
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['response'](content: $content);
}
