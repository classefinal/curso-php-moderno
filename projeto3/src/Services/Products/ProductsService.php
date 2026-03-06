<?php

function getActiveProducts(mysqli $connection, int $limit = 10): array
{
    $results = dbPrepareAndExecute($connection, 'SELECT * FROM products WHERE active = true LIMIT ?', [
        [
            'type' => 'i',
            'value' => $limit
        ]
    ]);

    if (mysqli_num_rows($results) === 0) {
        return [];
    }

    return mysqli_fetch_all($results, MYSQLI_ASSOC);
}
