<?php

/**
 * @psalm-import-type Route from Types
 * 
 * @var string $title
 * @var Route[] $routes
 * @var ?string $error
 * @var string $action
 */

require_once COMPONENTS . 'header.php';
?>

<main class="container mt-5" style="max-width: 500px;">
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php
        require_once  COMPONENTS . getRequirePath('Login/login_form.php');
    ?>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>