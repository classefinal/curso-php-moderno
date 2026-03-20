<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * @psalm-import-type Route from types
 * 
 * @var string $title
 * @var Route[] $routes
 * @var Product $product
 */

 require_once COMPONENTS . 'header.php'
?>

<main class="container">
    <section>
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
</main>

<?php require_once COMPONENTS . 'footer.php' ?>