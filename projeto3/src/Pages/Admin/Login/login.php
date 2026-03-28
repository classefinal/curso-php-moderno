<?php

/**
 * @psalm-import-type Route from types
 * 
 * @var string $title
 * @var Route[] $routes
 * @var ?string $error
 */

require_once COMPONENTS . 'header.php';
?>

<main class="container mt-5" style="max-width: 500px;">
    <h1><?= $title ?></h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"></div>
    <?php endif; ?>
    <?php
        $action = '/admin/login';

        require_once  COMPONENTS . getRequirePath('Login/login_form.php');
    ?>
</main>

<?php require_once COMPONENTS . 'footer.php' ?>