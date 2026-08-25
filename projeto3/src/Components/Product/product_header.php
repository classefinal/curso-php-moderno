<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from Types
 * 
 * @var Product $product
 */
?>
<div class="col-12">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-4 col-lg-6">
            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="d-block w-100" />
        </div>
        <div class="col-12 col-sm-12 col-md-8 col-lg-6">
            <h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8') ?></p>
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
                    <button type="submit" class="btn btn-primary w-100" title="Comprar <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Comprar
                    </button>
                </form>
            </p>
        </div>
    </div>
</div>