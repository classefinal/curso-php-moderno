<?php
 
/**
 * @var string $action
 */
?>
<form action="<?= $action ?>" method="POST" autocomplete="off">
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" required id="email" name="email" autofocus autocomplete="off" placeholder="E-mail" >
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Senha</label>
        <input type="password" class="form-control" required id="password" name="password" placeholder="Senha">
    </div>
    <button type="submit" class="btn btn-primary w-100">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar
    </button>
</form>