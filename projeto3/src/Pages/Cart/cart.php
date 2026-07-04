<?php

declare(strict_types=1);

/**
 * @psalm-import-type CartItem from types
 * @psalm-import-type Route from types
 *
 * @var string $title
 * @var Route[] $routes
 * @var CartItem[] $items
 * @var int $total
 */

require_once COMPONENTS . 'header.php';

?>

<main class="container mt-5" style="max-width: 800px;">
    <h2 class="mb-4">
        <i class="fa-solid fa-cart-shopping"></i>
        Carrinho de compras
    </h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            <p class="mb-0">Seu carrinho está vazio.</p>
        </div>
        <a href="/produtos" class="btn btn-primary">
            <i class="fa-solid fa-shopping-bag"></i>
            Ver produtos
        </a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Produto</th>
                        <th scope="col">Preço</th>
                        <th scope="col">Quantidade</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" style="width: 64px; height: 64px; object-fit: cover;" class="rounded">
                                    <div>
                                        <a href="/produtos/<?= $item['product_id'] ?>" class="text-decoration-none fw-semibold">
                                            <?= $item['name'] ?>
                                        </a>
                                        <small class="d-block text-muted"><?= $item['description_line'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold">
                                R$ <?= number_format((int)$item['price'] / 100, 2, ',', '.') ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <form method="post" action="/carrinho/atualizar" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Diminuir quantidade">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </form>
                                    <span class="mx-3 fw-bold fs-5"><?= (int)$item['quantity'] ?></span>
                                    <form method="post" action="/carrinho/atualizar" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="action" value="increase">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Aumentar quantidade">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="fw-semibold">
                                R$ <?= number_format((int)$item['price'] * (int)$item['quantity'] / 100, 2, ',', '.') ?>
                            </td>
                            <td>
                                <form method="post" action="/carrinho/remover" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover item">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <div class="bg-light p-4 rounded" style="min-width: 280px;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-5">Total</span>
                    <span class="fs-4 fw-bold text-primary">
                        R$ <?= number_format($total / 100, 2, ',', '.') ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>
