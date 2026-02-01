<?php

/**
 * @psalm-import-type Product from types
 */

/** @var Product[] $products */
/** @var string $baseUrl */
?>

<?php if (empty($products)): ?>
    <?php include 'nodata.php' ?>
<?php else: ?>
    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Unidade</th>
                <th>Quantidade</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <?php require 'row.php' ?>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>