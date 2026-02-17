<?php

/**
 * @psalm-import-type StmArg from types
 */

function dbConnect(): mysqli
{
    $host = getenv('DB_SERVER');
    $port = getenv('DB_PORT');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    $database = getenv('DB_DATABASE');

    $connection = mysqli_connect($host, $user, $password, $database, intval($port));

    if ($connection === false) {
        die('Erro de coneão com o banco de dados ' . mysqli_connect_error());
    }

    mysqli_set_charset($connection, 'utf8mb4');

    return $connection;
}

function dbClose(mysqli $connection): void
{
    mysqli_close($connection);
}

function dbExecuteStm(mysqli $connection, string $stm): mysqli_result|bool
{
    return mysqli_query($connection, $stm);
}

/**
 * @param mysqli $connection
 * @param string $stm
 * @param StmArg[] $args
 * @return mysqli_result|boolean
 */
function dbPrepareAndExecuteStm(mysqli $connection, string $stm, array $args): mysqli_result|bool
{
    $preparedStm = mysqli_prepare($connection, $stm);
    $values = [];
    $types = '';

    foreach($args as $arg) {
        $types .= $arg['type'];
        $values[] = $arg['value'];
    }
    
    mysqli_stmt_bind_param($preparedStm, $types, ...$values);

    mysqli_stmt_execute($preparedStm);

    return mysqli_stmt_get_result($preparedStm);
}
