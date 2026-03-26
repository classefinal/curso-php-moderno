<?php

/**
 * @psalm-import-type Product from types
 */

/**

 * @param mysqli $connection
 * @return Product[]
 */
function getRandomActiveProducts(mysqli $connection): array
{
    $query = '
        SELECT p.id, p.name, p.price, p.image, c.name as category_name
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id
        WHERE p.active = true AND c.active = true
        ORDER BY RAND()
        LIMIT 6
    ';

    $result = dbPrepareAndExecute($connection, $query);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
