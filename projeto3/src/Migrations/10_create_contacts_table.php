<?php

/**
 * @psalm-import-type Migration from Types
 */

/** @var Migration $migration */
$migration = [
    'up' => function (mysqli $connection): void {
        dbExecuteStm($connection, "
            CREATE TABLE contacts (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;
        ");
    }
];

return $migration;
