<?php

/**
 * @psalm-import-type Product from types
 */

/** @var Product $product */
/** @var string $baseUrl */
?>

<tr>
    <td><?= $product['name'] ?></td>
    <td><?= $product['unit'] ?></td>
    <td><?= $product['quantity'] ?></td>
    <td>
        <a
            href="<?= makeSearch($baseUrl, $product['name']) ?>"
            title="Buscar no google"
            target="_blank"
            rel="noopener noreferrer">
            Buscar no Google
        </a>
    </td>
</tr>