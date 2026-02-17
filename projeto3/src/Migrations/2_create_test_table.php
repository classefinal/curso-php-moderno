<?php

/**
 * @psalm-import-type Migration from types
 */

/**
 * @var Migration $migration
 */
$migration = [
    'up' => function (mysqli $connection): void {
        dbExecuteStm($connection, "
            CREATE TABLE test (
                id INT NOT NULL AUTO_INCREMENT, 
                PRIMARY KEY (id)
            );
        ");
    }
];

return $migration;