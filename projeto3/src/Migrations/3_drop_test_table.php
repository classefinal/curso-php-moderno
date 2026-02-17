<?php

/**
 * @psalm-import-type Migration from types
 */

/**
 * @var Migration $migration
 */
$migration = [
    'up' => function (mysqli $connection): void {
        dbExecuteStm($connection, "DROP TABLE test");
    }
];

return $migration;