<?php

/**
 * @psalm-import-type Configs from types
 */

/**
 * @param Configs $configs
 * @param array $args
 * @return void
 */
function handleAdminLoginErrorEvent(array $configs, array $args): void
{
    if (empty($args['email']) || empty($args['date'])) {
        return;
    }

    $configs['defer'](function () use ($args) {
        $date = $args['date'];
        $email = $args['email'];

        $folder = BASE_PATH . DIRECTORY_SEPARATOR . 'logs';

        if(!file_exists($folder) && !mkdir($folder)) {
            return;
        }

        file_put_contents(
            $folder . DIRECTORY_SEPARATOR . 'adminLoginErrors.txt',
            "$date: $email" . PHP_EOL,
            FILE_APPEND
        );
    });
}
