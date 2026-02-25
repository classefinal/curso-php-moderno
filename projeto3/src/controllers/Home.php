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
    $configs['defer'](function() {
        sleep(10);
        file_put_contents(BASE_PATH . DIRECTORY_SEPARATOR . "log.txt", "Terminou\n");
    });

    makePage('home', [
        'title' => 'Página inicial',
        'routes' => getMenuItens($configs['routes'], $uri),
    ]);

    setcookie('teste', '1');

    $configs['response'](200, '123');
}
