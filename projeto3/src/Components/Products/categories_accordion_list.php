<?php

/**
 * @psalm-import-type Category from types
 * 
 * @var Category[] $categories
 */
?>
<menu class="list-unstyled">
    <?php foreach ($categories as $category): ?>
        <li>
            <a href="/produtos?categoryId=<?= $category['id'] ?>" class="text-decoration-none" title="Ir para categoria <?= $category['name'] ?>">
                <?php if (isset($_GET['categoryId']) && $_GET['categoryId'] == $category['id']): ?>
                    <strong><?= $category['name'] ?></strong>
                <?php else: ?>
                    <?= $category['name'] ?>
                <?php endif ?>
            </a>
        </li>
    <?php endforeach ?>
</menu>