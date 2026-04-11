<?php

/**
 * @psalm-import-type Product from types
 * 
 * @var Product[] $randomProducts
 */
?>
<div class="row">
    <?php foreach ($randomProducts as $product): ?>
        <div class="col-6 col-sm-4 col-md-4 col-lg-2 mt-3">
            <div class="card h-100">
                <a href="/produtos/<?= $product['id'] ?>" title="<?= $product['name'] ?>" class="text-decoration-none">
                    <img src="<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                </a>
                <div class="card-body">
                    <p class="h6 card-title mb-2">
                        <a href="/produtos/<?= $product['id'] ?>" title="<?= $product['name'] ?>" class="text-decoration-none">
                            <?= $product['name'] ?>
                        </a>
                    </p>
                    <div class="fw-bold text-primary h5">
                        <a href="/produtos/<?= $product['id'] ?>" title="<?= $product['name'] ?>" class="text-decoration-none">
                            <strong>R$ <?= number_format($product['price'] / 100, 2, ',', '.') ?></strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>