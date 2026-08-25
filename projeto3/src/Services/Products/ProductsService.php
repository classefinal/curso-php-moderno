<?php

/**
 * @psalm-import-type ActiveProductsList from Types
 * @psalm-import-type StmArg from Types
 * @psalm-import-type Product from Types
 */

function getActiveProductsQuery(?int $categoryId): string
{
    $query = '
        SELECT p.*, c.name as category_name FROM products p 
        INNER JOIN categories c ON p.category_id = c.id 
        WHERE p.active = true AND c.active = true
    ';

    if ($categoryId) {
        return $query . ' AND c.id = ? ';
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
        'limit' => $limit,
        'page' => $page,
        'query' => $query,
        'params' => $params,
        'categoryId' => $categoryId
    ];
}

/**
 * @param mysqli $connection
 * @return ActiveProductsList
 */
function getActiveProducts(mysqli $connection): array
{
    [
        'categoryId' => $categoryId,
        'page' => $page,
        'limit' => $limit,
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

/**
 * @param mysqli $connection
 * @param string $uri
 * @return Product|null
 */
function getProductById(mysqli $connection, string $uri): ?array
{
    $routeItems = explode('/', $uri);

    $productId = filter_var(array_last($routeItems), FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'default' => null
        ]
    ]);

    $productId = explode('/', $uri)
    |> array_last(...)
    |> (fn($productUrlId) => filter_var($productUrlId, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'default' => null
        ]
    ]));

    if (is_null($productId)) {
        return null;
    }

    $result = dbPrepareAndExecute(
        $connection,
        '
        SELECT 
            p.*, 
            c.name as category_name 
        FROM 
            products p 
            INNER JOIN categories c ON p.category_id = c.id 
        WHERE 
            p.id = ? 
            AND p.active = true 
            AND c.active = true
        LIMIT 1
        ',
        [
            [
                'type' => 'i',
                'value' => $productId
            ]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($result);
}
