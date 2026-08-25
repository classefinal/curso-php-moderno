<?php

/**
 * @psalm-import-type Category from Types
 * 
 * @var Category[] $categories
 * @var int $limit
 * @var ?int $categoryId
 */
?>

<aside class="col-12 col-sm-12 col-md-4 col-lg-3 mt-4">
    <p>Opções</p>
    <div class="accordion" id="filterOptions">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Quantidade de itens
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#filterOptions">
                <div class="accordion-body">
                    <menu class="list-unstyled">
                        <li>
                            <a href="/produtos?limit=10<?= $categoryId ? '&categoryId=' . $categoryId : '' ?>" class="text-decoration-none">
                                <?php if (!isset($limit) || $limit === 10): ?>
                                    <strong>10 itens por página</strong>
                                <?php else: ?>
                                    10 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/produtos?limit=20<?= $categoryId ? '&categoryId=' . $categoryId : '' ?>" class="text-decoration-none">
                                <?php if (isset($limit) && $limit === 20): ?>
                                    <strong>20 itens por página</strong>
                                <?php else: ?>
                                    20 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/produtos?limit=30<?= $categoryId ? '&categoryId=' . $categoryId : '' ?>" class="text-decoration-none">
                                <?php if (isset($limit) && $limit === 30): ?>
                                    <strong>30 itens por página</strong>
                                <?php else: ?>
                                    30 itens por página
                                <?php endif; ?>
                            </a>
                        </li>
                    </menu>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Categorias de produtos
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#filterOptions">
                <div class="accordion-body">
                    <?php require_once 'categories_accordion_list.php'; ?>
                </div>
            </div>
        </div>
    </div>
</aside>