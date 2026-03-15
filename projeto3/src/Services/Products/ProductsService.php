<?php

/**
 * @psalm-import-type ActiveProductsList from types
 * @psalm-import-type StmArg from types
 */

function getActiveProductsQuery(?int $categoryId): string
{
    $query = 'SELECT p.*, c.name as category_name FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE p.active = true AND c.active = true';

    if ($categoryId) {
        return $query . ' AND c.id = ?';
    }

    return $query;
}

/**
 * @return array{page: int, limit: int, query: string, params: StmArg[], categoryId: ?int}
 */
function getActiveProductsParams(): array
{
    $categoryId = filter_var($_GET['categoryId'] ?? null, FILTER_VALIDATE_INT, [
        'options' => [
            'default' => null,
            'min_range' => 1,
        ]
    ]);

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

    $query = getActiveProductsQuery($categoryId) . ' LIMIT ? OFFSET ? ';

    $params = [
        [
            'type' => 'i',
            'value' => $limit
        ],
        [
            'type' => 'i',
            'value' => ($page - 1) * $limit
        ]
    ];

    if ($categoryId) {
        array_unshift($params, [
            'type' => 'i',
            'value' => $categoryId
        ]);
    }

    return [
        'page' => $page,
        'limit' => $limit,
        'categoryId' => $categoryId,
        'query' => $query,
        'params' => $params,
    ];
}

/**
 * @param mysqli $connection
 * @return ActiveProductsList
 */
function getActiveProducts(mysqli $connection): array
{
    [
        'limit' => $limit,
        'page' => $page,
        'categoryId' => $categoryId,
        'query' => $query,
        'params' => $params
    ] = getActiveProductsParams();

    $returnData = [
        'limit' => $limit,
        'page' => $page,
        'products' => [],
        'categoryId' => $categoryId
    ];

    $results = dbPrepareAndExecute(
        $connection,
        $query,
        $params
    );

    if (mysqli_num_rows($results) === 0) {
        return $returnData;
    }

    $returnData['products'] = mysqli_fetch_all($results, MYSQLI_ASSOC);

    return $returnData;
}
