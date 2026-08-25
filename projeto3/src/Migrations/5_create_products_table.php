<?php

/**
 * @psalm-import-type Migration from Types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "
     CREATE TABLE products (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
        name VARCHAR(255) NOT NULL, 
        description TEXT NOT NULL, 
        active BOOLEAN NOT NULL, 
        stock INT UNSIGNED NOT NULL, 
        price INT NOT NULL, 
        image TEXT NOT NULL, 
        category_id INT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
        PRIMARY KEY (id)
      ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;

      ");

      dbExecuteStm($connection, "ALTER TABLE `products` ADD FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }
];

return $migration;