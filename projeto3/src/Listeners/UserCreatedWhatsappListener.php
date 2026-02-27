<?php

/**
 * @psalm-import-type Configs from types
 */

/**
 * @param Configs $configs
 * @param array $args
 * @return void
 */
function handleUserCreatedWhatsappEmailEvent(array $configs, array $args): void {
    $configs['defer'](fn() => file_put_contents(BASE_PATH . DIRECTORY_SEPARATOR . $args['name'], $args['phone'], FILE_APPEND));
}
