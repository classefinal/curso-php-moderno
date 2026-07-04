<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
    'up' => function (mysqli $connection): void {
        dbExecuteStm($connection, "
            CREATE TABLE carts (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY (user_id)
            ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;
        ");

        dbExecuteStm($connection, "
            ALTER TABLE carts ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        dbExecuteStm($connection, "
            CREATE TABLE cart_items (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                cart_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                quantity INT UNSIGNED NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;
        ");

        dbExecuteStm($connection, "
            ALTER TABLE cart_items ADD FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        dbExecuteStm($connection, "
            ALTER TABLE cart_items ADD FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }
];

return $migration;
