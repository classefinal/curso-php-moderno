<?php

declare(strict_types=1);

function makeHome(): void
{
    makePage('home', [
        'title' => 'Página inicial'
    ]);
}
