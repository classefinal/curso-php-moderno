<?php require_once COMPONENTS . 'header.php'; ?>

<main class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Perfil do Usuário</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['profile_updated'])): ?>
        <div class="alert alert-success">Atualizado com sucesso</div>
    <?php elseif (isset($result['success']) && $result['success']): ?>
        <div class="alert alert-success">Perfil atualizado com sucesso!</div>
    <?php endif; ?>
    <form method="post" action="/usuario/perfil">
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
        </div>
        <hr>
        <h5>Alterar senha</h5>
        <div class="mb-3">
            <label for="old_password" class="form-label">Senha atual</label>
            <input type="password" class="form-control" id="old_password" name="old_password">
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nova senha</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Repita a nova senha</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <button type="submit" class="btn btn-primary w-100">Salvar alterações</button>
    </form>
</main>

<?php require_once COMPONENTS . 'footer.php'; ?>