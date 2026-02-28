<?php

declare(strict_types=1);

/**
 * @var string $productId
 * @var string $regex
 * @var string $controller
 */

 require_once COMPONENTS . 'header.php'
?>

<p>Página do produto com id <?= $productId ?> usando a regex <?= htmlentities($regex) ?> dentro do controller <?= $controller ?></p>

<?php require_once COMPONENTS . 'footer.php' ?>