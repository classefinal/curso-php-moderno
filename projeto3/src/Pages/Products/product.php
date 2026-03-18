<?php

declare(strict_types=1);

/**
 * @psalm-import-type Product from types
 * @psalm-import-type Category from types
 * 
 * @var string $productId
 * @var string $regex
 * @var string $controller
 * @var Product&Category&array{category_name: string} $product
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

        <div class="row">
            <?php require_once COMPONENTS . getRequirePath('Product/product_description.php'); ?>
        </div>
    </section>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>