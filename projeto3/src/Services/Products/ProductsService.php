<?php

/**
 * @psalm-import-type ActiveProductsList from types
 */

/**
 * @param mysqli $connection
 * @return ActiveProductsList
 */
function getActiveProducts(mysqli $connection): array
{
    $limit = filter_var($_GET['limit'] ?? null, FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 10,
            'min_range' => 5,
            'max_range' => 30
        ]
    ]);

    $page = filter_var($_GET['page'] ?? null, FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 1,
            'min_range' => 1,
        ]
    ]);

    $returnData = [
        'limit' => $limit,
        'page' => $page,
        'products' => []
    ];

    $results = dbPrepareAndExecute(
        $connection, 
        'SELECT p.*, c.name as category_name FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE p.active = true AND c.active = true LIMIT ? OFFSET ?',
        [
            [
                'type' => 'i',
                'value' => $limit
            ],
            [
                'type' => 'i',
                'value' => ($page - 1) * $limit
            ]
        ]
    );

    if(mysqli_num_rows($results) === 0) {
        return $returnData;
    }

    $returnData['products'] = mysqli_fetch_all($results, MYSQLI_ASSOC);

    return $returnData;
}
