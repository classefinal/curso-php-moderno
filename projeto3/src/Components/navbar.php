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
        <?php if (isset($_SESSION['admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/admin/dashboard" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Gerenciar
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="/admin/dashboard" title="Ir para Administração">Administração</a></li>
              <li><a class="dropdown-item" href="/admin/logout" title="Sair">Sair</a></li>
            </ul>
          </li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/usuario/perfil" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Gerenciar
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="/usuario/perfil" title="Ir para Perfil">Perfil</a></li>
              <li><a class="dropdown-item" href="/usuario/logout" title="Sair">Sair</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>