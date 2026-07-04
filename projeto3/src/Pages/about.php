<?php

/**
 * @var string $title
 * @var Route[] $routes
 * @var ?string $success
 * @var ?string $error
 */

require_once COMPONENTS . 'header.php' ?>

<main class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?= $title ?></h1>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <form action="/sobre" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" required id="name" name="name" placeholder="Seu nome">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" required id="email" name="email" placeholder="seu@email.com">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" required id="phone" name="phone" placeholder="(00)94878-4541">
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-paper-plane"></i> Enviar
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14698.563962755185!2d-47.0657900249474!3d-22.9069989606735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c8c8d1f1b1b1b1%3A0x1b1b1b1b1b1b1b1b!2sCampinas!5e0!3m2!1spt-BR!2sbr!4v1"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="w-100 h-100"
                style="min-height: 400px; border: 0;">
            </iframe>
        </div>
    </div>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>
