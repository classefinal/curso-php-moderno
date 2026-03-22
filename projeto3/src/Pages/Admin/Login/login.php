<?php require_once COMPONENTS . 'header.php'; ?>

<main class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">Login Administrativo</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post" action="/admin/login">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
</main>

<?php require_once COMPONENTS . 'footer.php'; ?>
