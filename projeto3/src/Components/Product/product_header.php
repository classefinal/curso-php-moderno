<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * @psalm-import-type Category from types
 * 
 * @var Product&Category&array{category_name: string} $product
 */
?>
<div class="row mt-3">
    <div class="col-12 col-sm-12 col-md-4 col-lg-6">
        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="d-block w-100" />
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-6">
        <h1><?= $product['name'] ?></h1>
        <p><?= $product['short_description'] ?></p>
        <p class="h2 text-primary">
            <strong>R$ <?= number_format($product['price'] / 100, 2, ',', '.') ?></strong>
        </p>
        <p>
            <?php if ($product['stock'] > 0): ?>
                <span class="badge text-bg-success">Estoque disponível</span>
            <?php else: ?>
                <span class="badge text-bg-danger">Estoque disponível</span>
            <?php endif ?>
        </p>
        <p>
            <a href="/produtos/<?= $product['id'] ?>" class="btn btn-primary w-100" title="Ir para produto <?= $product['name'] ?>">
                <i class="fa-solid fa-cart-shopping"></i>
                Comprar
            </a>
        </p>
    </div>
</div>