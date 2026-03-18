<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * @psalm-import-type Category from types
 * 
 * @var Product&Category&array{category_name: string} $product
 */
?>
<div class="col-12">
    <?= $product['description'] ?>
</div>