<?php

declare(strict_types=1);

function dbConnect(): mysqli
{
    $host = getenv('DB_SERVER');
    $port = getenv('DB_PORT');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    $database = getenv('DB_DATABASE');

    $connection = mysqli_connect($host, $user, $password, $database, intval($port));

    if ($connection === false) {
        die('Erro de conexão com o banco de dados: ' . mysqli_connect_error());
    }

    mysqli_set_charset($connection, 'utf8mb4');

    return $connection;
}

function dbClose(mysqli $connection): void
{
    mysqli_close($connection);
}
