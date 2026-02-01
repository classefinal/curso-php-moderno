<?php

function getPath(string $item): string
{
    return realpath(__DIR__) . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR;
}

function getComponentsPath(): string
{
    return getPath('components');
}

function getFunctionsPath(): string
{
    return getPath('functions');
}

function getPagesPath(): string
{
    return getPath('pages');
}
