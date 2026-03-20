<?php

/**
 * @psalm-import-type Migration from types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "
        ALTER TABLE products ADD short_description VARCHAR(255) NOT NULL DEFAULT '' AFTER description_line, ADD description_line VARCHAR(150) NOT NULL DEFAULT '' AFTER short_description;
      ");
    }
];

return $migration;