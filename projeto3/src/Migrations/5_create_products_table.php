<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "      
      CREATE TABLE products (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
        category_id INT UNSIGNED NOT NULL, 
        name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
        description TEXT NOT NULL,
        active BOOLEAN NOT NULL, 
        price INT UNSIGNED NOT NULL, 
        stock INT UNSIGNED NOT NULL, 
        image VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        PRIMARY KEY (id)
      ) ENGINE = InnoDb;
      ");

      dbExecuteStm($connection, "      
      ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE;
      ");
    }
];

return $migration;