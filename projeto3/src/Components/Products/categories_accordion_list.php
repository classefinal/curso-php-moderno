<?php

/**
 * @psalm-import-type Category from Types
 * 
 * @var Category[] $categories
 * @var ?int $categoryId
 */
?>
<menu class="list-unstyled">
    <?php foreach ($categories as $category): ?>
        <li>
            <a href="/produtos?categoryId=<?= $category['id'] ?>" class="text-decoration-none" title="Ir para categoria <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($categoryId) && $categoryId === $category['id']): ?>
                    <strong><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                <?php else: ?>
                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                <?php endif ?>
            </a>
        </li>
    <?php endforeach ?>
</menu>