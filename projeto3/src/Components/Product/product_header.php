<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * 
 * @var Product $product
 */
?>
<div class="col-12">
    <div class="row">
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
                    <span class="badge text-bg-success">Produto em estoque</span>
                <?php else: ?>
                    <span class="badge text-bg-danger">Fora de estoque</span>
                <?php endif ?>
            </p>
            <p>
                <form method="post" action="/carrinho/adicionar">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-primary w-100" title="Comprar <?= $product['name'] ?>">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Comprar
                    </button>
                </form>
            </p>
        </div>
    </div>
</div>