<?php

/**
 * @psalm-import-type Migration from Types
 */

/** @var Migration $migration */
$migration = [
    'up' => function(mysqli $connection): void{
      dbExecuteStm($connection, "DROP TABLE test");
    }
];

return $migration;