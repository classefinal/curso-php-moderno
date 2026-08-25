<?php

declare(strict_types=1);

/**
 * @psalm-import-type User from Types
 * @psalm-import-type Route from Types
 * 
 * @var string $title
 * @var Route[] $routes
 * @var ?string $error
 * @var User $user
 */

require_once COMPONENTS . 'header.php';

?>

<main class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Perfil do usuário</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['profile_updated'])): ?>
        <div class="alert alert-success">Perfil atualizado com sucesso</div>
    <?php endif; ?>

    <form method="post" action="/usuario/perfil">
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" required id="name" name="name" autofocus value="<?= htmlspecialchars($user['name']) ?>" placeholder="Nome">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" disabled value="<?= htmlspecialchars($user['email']) ?>" placeholder="E-mail">
        </div>
        <hr>
        <h5>Alterar senha</h5>
        <p>Preencha as informações abaixo, apenas se for alterar a senha</p>
        <div class="mb-3">
            <label for="old_password" class="form-label">Senha atual</label>
            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Senha atual">
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nova senha</label>
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nova senha">
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Repita a nova senha</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repita a nova senha">
        </div>
        <hr>
        <button type="submit" class="btn btn-primary w-100">Salvar</button>
    </form>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>