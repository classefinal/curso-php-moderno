<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "
      CREATE TABLE categories (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
        name VARCHAR(255) NOT NULL, 
        active BOOLEAN NOT NULL, 
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        PRIMARY KEY (id)
      ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;
      ");
    }
];

return $migration;