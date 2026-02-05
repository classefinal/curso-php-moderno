<?php

declare(strict_types=1);

function getPath(string $folder): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . SOURCES . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
}

function getComponentsPath(): string
{
    return getPath('Components');
}

function getControllersPath(): string
{
    return getPath('Controllers');
}

function getFunctionsPath(): string
{
    return getPath('Functions');
}

function getPagesPath(): string
{
    return getPath('Pages');
}

function getConfigsPath(): string
{
    return getPath('Configs');
}
