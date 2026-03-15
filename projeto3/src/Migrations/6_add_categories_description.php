<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "
        ALTER TABLE categories ADD description TEXT NOT NULL DEFAULT '' AFTER active;
      ");
    }
];

return $migration;