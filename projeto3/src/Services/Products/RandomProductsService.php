<?php

/**
 * @psalm-import-type Product from Types
 */

/**
 * @param mysqli $connection
 * @return Product[]
 */
function getRandomActiveProducts(mysqli $connection): array
{
    $query = '
        SELECT p.id, p.price, p.name, p.image 
        FROM products p 
        INNER JOIN categories c ON p.category_id = c.id 
        WHERE p.active = true AND c.active = true 
        ORDER BY RAND() 
        LIMIT 6
    ';

    $result = dbPrepareAndExecute($connection, $query);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
