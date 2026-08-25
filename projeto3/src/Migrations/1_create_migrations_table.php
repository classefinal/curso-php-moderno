<?php

/**
 * @psalm-import-type Migration from Types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
        $baseName = basename(__FILE__);

        dbExecuteStm($connection, "
            CREATE TABLE migrations (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
                name VARCHAR(255) NOT NULL, 
                executed BOOLEAN NOT NULL, 
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                PRIMARY KEY (id)
            );
        ");

        dbExecuteStm($connection, "
            INSERT INTO migrations (id, name, executed, created_at) VALUES (NULL, '$baseName', 1, CURRENT_TIMESTAMP)
        ");
    }
];

return $migration;