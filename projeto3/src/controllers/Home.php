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
function makeHome(array $configs, array $route, ?string $uri): void
{
    makePage('home', [
        'title' => 'Página inicial',
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    $configs['eventDispatcher']('UserCreated', [
        'name' => 'Gleison',
        'email' => 'gleison@site.com',
        'phone' => '+55119568798799'
    ]);
    
    $configs['response']();
}
