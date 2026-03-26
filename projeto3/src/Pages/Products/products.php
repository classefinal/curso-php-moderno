<?php

/**
 * @psalm-import-type Product from types
 * @psalm-import-type Route from types
 * @psalm-import-type Category from types
 * 
 * @var Product[] $products
 * @var Product[] $randomProducts
 * @var Category[] $categories
 * @var ?Category $activeCategory
 * @var Route[] $routes
 * @var string $title
 * @var int $limit
 * @var ?int $categoryId
 */

require_once COMPONENTS . 'header.php';

?>
<main class="container">
    <section>
        <div class="row mt-3">
            <div class="col">
                <h1><?= $title ?></h1>
                <?php if (!empty($activeCategory['description'])): ?>
                    <?= $activeCategory['description'] ?>
                <?php else: ?>
                    <p>Compre hoje mesmo com descontos incríveis.</p>
                <?php endif ?>
            </div>
        </div>
    </section>
    <section class="mb-5">
        <div class="row">
            <?php require_once COMPONENTS . getRequirePath('Products/aside_menu.php') ?>
            <?php require_once COMPONENTS . getRequirePath('Products/products_list.php') ?>
        </div>
    </section>
    <hr />
    <section class="mt-5">
        <h2>Produtos em destaque</h2>
        <?php require_once COMPONENTS . getRequirePath('RandomProducts/random_products_cards.php'); ?>
    </section>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>