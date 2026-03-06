<?php

/**
 * @psalm-import-type Product from types
 * 
 * @var Product[] $products
 * @var Route[] $routes
 * @var string $title
 */

require_once COMPONENTS . 'header.php';

?>

<main class="container">
    <section>
        <div class="row">
            <div class="col mt-3">
                <h1>Produtos</h1>
                <p>Compre hoje mesmo com descontos incríveis</p>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <?php require_once COMPONENTS . getRequirePath('Products/aside_menu.php') ?>
            <div class="col-12 col-sm-12 col-md-9">
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <?php require COMPONENTS . getRequirePath('Products/product_card.php') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>


<?php require_once COMPONENTS . 'footer.php' ?>