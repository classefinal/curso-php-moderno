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
            CREATE TABLE IF NOT EXISTS migrations (
                id INT NOT NULL AUTO_INCREMENT, 
                name VARCHAR(255) NOT NULL, 
                executed BOOLEAN NOT NULL, 
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            );
        ");

        dbExecuteStm($connection, "
            INSERT INTO migrations (id, name, executed) VALUES (NULL, '" . basename(__FILE__) . "', 1);
        ");
    }
];

return $migration;
