<?php

/**
 * @psalm-import-type Route from types
 */

/**
 * @var Route[] $routes
 * @var string $uri
 */

?>
<nav class="navbar navbar-expand-lg bg-body-tertiary bg-black" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Meu site</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <menu class="navbar-nav m-0">
                <?php foreach ($routes as $route): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= !empty($route['active']) ? 'active' : '' ?>" aria-current="page" href="<?= $route['value'] ?>" title="Ir para <?= $route['label'] ?>"><?= $route['label'] ?></a>
                    </li>
                <?php endforeach ?>
            </menu>
        </div>
    </div>
</nav>