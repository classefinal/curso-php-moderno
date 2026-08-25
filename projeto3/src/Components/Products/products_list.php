<?php

/**
 * @psalm-import-type Product from Types
 * @psalm-import-type EmptyLinkConfig from Types
 * 
 * @var Product[] $products
 */

?>

<div class="col-12 col-sm-12 col-md-8 col-lg-9 mt-4">
    <?php if (empty($products)): ?>
        <?php
        $emptyTitle = 'Nenhum item encontrado para a página especificada.';
        $emptySubtitle = 'Tente acessar a nossa página de produtos.';

        /** @var EmptyLinkConfig $emptyLinkConfig */
        $emptyLinkConfig = [
            'link' => '/produtos',
            'text' => 'Ir para produtos',
            'title' => 'Ir para a página de produtos',
            'icon' => 'fa-solid fa-shopping-cart'
        ];

        require_once COMPONENTS . getRequirePath('Empty/empty.php');
        ?>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <?php require 'product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif ?>
</div>