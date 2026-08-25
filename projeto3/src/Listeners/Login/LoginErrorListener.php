<?php

/**
 * @psalm-import-type Configs from Types
 */

/**
 * @param Configs $configs
 * @param array $args
 * @return void
 */
function handleLoginErrorEvent(array $configs, array $args): void
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
            $folder . DIRECTORY_SEPARATOR . date('Y-m-d') . '-loginErrors.txt',
            "$date: $email" . PHP_EOL,
            FILE_APPEND
        );
    });
}
