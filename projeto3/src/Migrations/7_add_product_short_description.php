<?php

/**
 * @psalm-import-type Migration from Types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "
        ALTER TABLE products ADD short_description VARCHAR(255) NOT NULL DEFAULT '', ADD description_line VARCHAR(150) NOT NULL DEFAULT '';
      ");
    }
];

return $migration;