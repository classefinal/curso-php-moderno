<?php require_once COMPONENTS . 'header.php'; ?>

<main class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">Login Administrativo</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php 
        $action = '/admin/login';
        
        require COMPONENTS . 'Login/login_form.php'; 
    ?>
</main>

<?php require_once COMPONENTS . 'footer.php'; ?>
