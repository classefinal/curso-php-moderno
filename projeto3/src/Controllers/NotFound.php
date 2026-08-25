<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from Types
 * @psalm-import-type Configs from Types
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
        'routes' => getMenuItens($configs['routes'], $uri, $route),
    ]);

    $configs['response'](404, $content);
}
