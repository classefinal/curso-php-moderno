<?php

declare(strict_types=1);

function loadEnv(string $envPath): void
{
    if (!file_exists($envPath)) {
        return;
    }

    $fileHandle = fopen($envPath, 'r');

    if ($fileHandle === false) {
        return;
    }

    while (($line = fgets($fileHandle)) !== false) {
        $line = trim($line);

        if (empty($line) || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');

        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if (!empty($key) && getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }

    fclose($fileHandle);
}
