<?php

/**
 * @psalm-import-type Product from types
 */

/** @var Product $product */
/** @var string $searchUrl */
?>

<tr>
    <td><?= $product['name'] ?></td>
    <td><?= $product['unit'] ?></td>
    <td><?= $product['quantity'] ?></td>
    <td>
        <a
            href="<?= makeSearchParameter($searchUrl, $product['name']) ?>"
            target="_blank"
            rel="noopener noreferrer"
            title="Clique para comprar <?= $product['name'] ?>">
            Clique para comprar
        </a>
    </td>
</tr>