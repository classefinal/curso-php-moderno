<?php

function getPath(string $folder): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . SOURCES . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
}

function getComponentsPath(): string
{
    return getPath('components');
}

function getControllersPath(): string
{
    return getPath('controllers');
}

function getFunctionsPath(): string
{
    return getPath('functions');
}

function getPagesPath(): string
{
    return getPath('pages');
}

function getConfigsPath(): string
{
    return getPath('configs');
}
