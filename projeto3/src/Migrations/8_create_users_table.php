<?php

/**
 * Migration para criar a tabela users e inserir um usuário padrão
 */

/** @var Migration $migration */
$migration = [
    'up' => function (mysqli $connection): void {
        $createTable = "
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                admin TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
        ";

        dbExecuteStm($connection, $createTable);

        $name = 'Administrador';
        $email = 'admin@admin.com';
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        $active = 1;
        $admin = 1;

        dbPrepareAndExecute(
            $connection,
            'INSERT INTO users (name, email, password, active, admin) VALUES (?, ?, ?, ?, ?)',
            [
                ['type' => 's', 'value' => $name],
                ['type' => 's', 'value' => $email],
                ['type' => 's', 'value' => $password],
                ['type' => 'i', 'value' => $active],
                ['type' => 'i', 'value' => $admin]
            ]
        );
    }
];

return $migration;
