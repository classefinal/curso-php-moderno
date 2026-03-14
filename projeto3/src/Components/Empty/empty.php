<?php

/**
 * @psalm-import-type EmptyLinkConfig from types
 * 
 * @var ?string $emptyTitle
 * @var ?string $emptySubtitle
 * @var ?EmptyLinkConfig $emptyLinkAction
 */

?>
<div class="row justify-content-center">
    <div class="col-8">
        <img src="images/empty.png" alt="Sem itens no carrinho" class="d-block w-100">
    </div>
</div>
<div class="row">
    <div class="col-12 text-center">
        <h1><?= !empty($emptyTitle) ? $emptyTitle : 'Nenhum item encontrado' ?></h1>

        <?php if (!empty($emptySubtitle)): ?>
            <p><?= $emptySubtitle ?></p>
        <?php endif ?>

        <?php if (!empty($emptyLinkAction['link']) && !empty($emptyLinkAction['text'])): ?>
            <p>
                <a
                    class="btn btn-primary"
                    href="<?= $emptyLinkAction['link'] ?>"
                    title="<?= !empty($emptyLinkAction['title']) ? $emptyLinkAction['title'] : $emptyLinkAction['text'] ?>">

                    <?php if (!empty($emptyLinkAction['icon'])): ?>
                        <i class="<?= $emptyLinkAction['icon'] ?>"></i>
                    <?php endif ?>

                    <?= $emptyLinkAction['text'] ?>
                </a>
            </p>
        <?php endif ?>
    </div>
</div>