<?php

/**
 * @psalm-import-type Product from types
 * 
 * @var Product $product
 */
?>

<div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4">
    <div class="card">
        <a href="/produtos/<?= $product['id'] ?>" title="Ir para produto <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none">
            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
        </a>
        <div class="card-body">
            <h5 class="card-title">
                <a href="/produtos/<?= $product['id'] ?>" title="Ir para produto <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none">
                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h5>
            <p class="card-text">
                <a href="/produtos/<?= $product['id'] ?>" title="Ir para produto <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none">
                    <?= htmlspecialchars($product['description_line'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </p>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 mt-2">
                    <form method="post" action="/carrinho/adicionar">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn btn-primary w-100" title="Comprar <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Comprar
                        </button>
                    </form>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 mt-2">
                    <a href="/produtos/<?= $product['id'] ?>" class="btn btn-outline-secondary w-100" title="Ir para produto <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fa-solid fa-circle-info"></i>
                        Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>