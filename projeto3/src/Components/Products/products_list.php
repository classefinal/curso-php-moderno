<?php

/**
 * @psalm-import-type Product from types
 * 
 * @var Product[] $products
 */

?>

<div class="col-12 col-sm-12 col-md-8 col-lg-9 mt-4">
    <div class="row">
        <?php foreach ($products as $product): ?>
            <?php require 'product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>