<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
  'up' => function (mysqli $connection): void {
    dbExecuteStm($connection, "
        CREATE TABLE users (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
          name VARCHAR(255) NOT NULL, 
          active BOOLEAN NOT NULL, 
          admin BOOLEAN NOT NULL, 
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
          updated_at DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
          PRIMARY KEY (id)
        ) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;

      ");

    $name = 'Administrador';
    $email = 'admin@admin.com';
    $password = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 16]);
    $active = 1;
    $admin = 1;

    dbPrepareAndExecute(
      $connection,
      '
        INSERT INTO users (name, email, password, active, admin) VALUES (?, ?, ?, ?, ?)
      ',
      [
        ['type' => 's', 'value' => $name],
        ['type' => 's', 'value' => $email],
        ['type' => 's', 'value' => $password],
        ['type' => 'i', 'value' => $active],
        ['type' => 'i', 'value' => $admin],
      ]
    );
  }
];

return $migration;
