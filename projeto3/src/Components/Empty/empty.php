<?php

/**
 * @psalm-import-type EmptyLinkConfig from types
 * 
 * @var ?string $emptyTitle
 * @var ?string $emptySubtitle
 * @var ?EmptyLinkConfig $emptyLinkConfig
 */
?>

<div class="row justify-content-center">
    <div class="col-8">
        <img src="images/empty.png" alt="Sem itens para exibir" class="d-block w-100">
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <h1>
                <?= !empty($emptyTitle) ? $emptyTitle : 'Nenhum item encontrado' ?>
            </h1>
            <?php if (!empty($emptySubtitle)): ?>
                <p>
                    <?= $emptySubtitle ?>
                </p>
            <?php endif ?>

            <?php if (!empty($emptyLinkConfig['link']) && !empty($emptyLinkConfig['text'])): ?>
                <p>
                    <a
                        class="btn btn-primary"
                        href="<?= $emptyLinkConfig['link'] ?>"

                        <?php if (!empty($emptyLinkConfig['title'])): ?>
                        title="<?= $emptyLinkConfig['title'] ?>"
                        <?php endif ?>>

                        <?php if (!empty($emptyLinkConfig['icon'])): ?>
                            <i class="<?= $emptyLinkConfig['icon'] ?>"></i>
                        <?php endif ?>
                        <?= $emptyLinkConfig['text'] ?>
                    </a>
                </p>
            <?php endif ?>
        </div>
    </div>
</div>