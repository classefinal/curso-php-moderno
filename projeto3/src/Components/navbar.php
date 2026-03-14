<?php

/**
 * @psalm-import-type Route from types
 * 
 * @var Route[] $routes
 */
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand" href="/">Meu site</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php foreach ($routes as $route): ?>
          <li class="nav-item">
            <a
              class="nav-link <?= isset($route['active']) && $route['active'] === true ? 'active' : '' ?>"
              aria-current="page"
              href="<?= $route['value'] ?>"
              title="Ir para <?= $route['label'] ?>">
              <?= $route['label'] ?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</nav>