<?php

function makePage(string $include, array $args): void
{
    extract($args);

    require COMPONENTS . 'header.php';

    require PAGES . $include . '.php';

    require COMPONENTS . 'footer.php';
}
