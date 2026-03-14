<?php

/**
 * @psalm-import-type Category from types
 */

/**
 * @param mysqli $connection
 * @return Category[]
 */
function getActiveCategories(mysqli $connection): array
{
    $results = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM categories WHERE active = true'
    );

    if (mysqli_num_rows($results) === 0) {
        return [];
    }

    return mysqli_fetch_all($results, MYSQLI_ASSOC);
}
