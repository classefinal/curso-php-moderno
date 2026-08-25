<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from Types
 * 
 * @var Product $product
 */
?>
<div class="col-12">
    <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>
</div>