<?php

/**
 * @psalm-import-type Product from types
 * 
 * @var Product $products
 */

?>
<div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4">
    <div class="card">
        <a href="/products/<?= $product['id'] ?>" title="Ir para <?= $product['name'] ?>">
            <img src="<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
        </a>
        <div class="card-body">
            <a href="/products/<?= $product['id'] ?>" title="Ir para <?= $product['name'] ?>" class="text-decoration-none">
                <h5 class="card-title"><?= $product['name'] ?></h5>
            </a>
            <a href="/products/<?= $product['id'] ?>" title="Ir para <?= $product['name'] ?>" class="text-decoration-none">
                <p class="card-text"><?= strlen($product['description']) > 100 ? substr($product['description'], 0, 100) . '...' : $product['description'] ?></p>
            </a>
            <div class="row mt-3">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 mb-3">
                    <a href="/products/<?= $product['id'] ?>" title="Ir para <?= $product['name'] ?>" class="btn btn-primary w-100">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Comprar
                    </a>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 mb-3">
                    <a href="/products/<?= $product['id'] ?>" title="Ir para <?= $product['name'] ?>" class="btn btn-outline-secondary w-100">
                        <i class="fa-solid fa-circle-info"></i>
                        Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>