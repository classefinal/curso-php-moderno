<?php

/**
 * @psalm-import-type Configs from types
 */

declare(strict_types=1);

define('SOURCES', 'src');
define('BASE_PATH', realpath(__DIR__));

// Initial config
require_once 'path.php';

// Constants of path
define('SERVICES', getServicesPath());
define('MIGRATIONS_PATH', getMigrationsPath());
define('BASE_MIGRATION', '1_create_migrations_table.php');

// Requires
require_once SERVICES . 'environment.php';
require_once SERVICES . 'db.php';

loadEnv(BASE_PATH . DIRECTORY_SEPARATOR . '.env');

$connection = dbConnect();
$migrations = scandir(MIGRATIONS_PATH);

function migrationsTableExists(mysqli $connection): bool
{
    $hasMigrationsTableResult = dbExecuteStm($connection, "SHOW TABLES LIKE 'migrations'");

    return $hasMigrationsTableResult === false ? false : mysqli_num_rows($hasMigrationsTableResult) > 0;
}

function migrationCanBeExecuted(array $migration, bool $hasMigrationsTable, string $migrationName): bool
{
    if (empty($migration['up']) || (!$hasMigrationsTable && $migrationName !== BASE_MIGRATION)) {
        return false;
    }

    return true;
}

function migrationAlreadyExecuted(mysqli $connection, string $migrationName): bool
{
    $params = [
        ['type' => 's', 'value' => mysqli_real_escape_string($connection, $migrationName)]
    ];

    $result = dbPrepareAndExecuteStm($connection, "SELECT * FROM migrations WHERE name=?", $params);

    return $result === false ? false : mysqli_num_rows($result) > 0;
}

$hasMigrationsTable = migrationsTableExists($connection);

foreach ($migrations as $migrationName) {
    $filePath = MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $migrationName;

    if (!is_file($filePath)) {
        continue;
    }

    $migration = require_once $filePath;

    if (!migrationCanBeExecuted($migration, $hasMigrationsTable, $migrationName)) {
        continue;
    }

    if (!$hasMigrationsTable && $migrationName === BASE_MIGRATION) {
  
        $migration['up']($connection);

        $hasMigrationsTable = true;

        continue;
    }

    if (migrationAlreadyExecuted($connection, $migrationName)) {
        continue;
    }

    $migration['up']($connection);

    dbExecuteStm($connection, "
        INSERT INTO migrations (id, name, executed) VALUES (NULL, '" . $migrationName . "', 1);
    ");
}

dbClose($connection);
