<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from Types
 * @psalm-import-type Route from Types
 * 
 * @var string $title
 * @var Route[] $routes
 * @var Product $product
 * @var Product[] $randomProducts
 */

require_once COMPONENTS . 'header.php'
?>

<main class="container">
    <section class="mb-5">
        <div class="row mt-3">
            <?php require_once COMPONENTS . getRequirePath('Product/product_breadcrumb.php'); ?>
        </div>
        <div class="row mt-3">
            <?php require_once COMPONENTS . getRequirePath('Product/product_header.php'); ?>
        </div>
        <div class="row mt-3">
            <?php require_once COMPONENTS . getRequirePath('Product/product_description.php'); ?>
        </div>
    </section>
    <hr />
    <section class="mt-5">
        <h2>Produtos em destaque</h2>
        <?php require_once COMPONENTS . getRequirePath('RandomProducts/random_products_cards.php'); ?>
    </section>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>