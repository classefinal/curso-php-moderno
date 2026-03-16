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
        'SELECT * FROM categories WHERE active = true ORDER BY name'
    );

    if (mysqli_num_rows($results) === 0) {
        return [];
    }

    return mysqli_fetch_all($results, MYSQLI_ASSOC);
}

/**
 * @param mysqli $connection
 * @param int $categoryId
 * @return ?Category
 */
function getActiveCategoryById(mysqli $connection, int $categoryId): array
{
    $results = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM categories WHERE id = ? LIMIT 1',
        [
            [
                'type' => 'i',
                'value' => $categoryId
            ]
        ]
    );

    if (mysqli_num_rows($results) === 0) {
        return [];
    }

    return mysqli_fetch_assoc($results);
}
