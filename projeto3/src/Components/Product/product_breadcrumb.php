<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * 
 * @var Product $product
 */
?>
<div class="col-12">
    Você está em:
    <a
        href="/produtos?categoryId=<?= $product['category_id'] ?>"
        title="Ir para categoria <?= $product['category_name'] ?>"
    >
        <strong><?= $product['category_name'] ?></strong>
    </a>
</div>