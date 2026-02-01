<?php

/**
 * @psalm-import-type Product from types
 */

/** @var Product[] $products */
?>

<?php if (empty($products)): ?>
    <?php require 'nodata.php' ?>
<?php else: ?>
    <table border="1">
        <thead>
            <?php require 'table_reader.php' ?>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <?php require 'row.php' ?>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <?php require 'table_reader.php' ?>
        </thead>
    </table>
<?php endif ?>