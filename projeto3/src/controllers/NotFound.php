<?php

declare(strict_types=1);

function makeNotFound(): void
{
    makePage('not_found', [
        'title' => 'Página não encontrada'
    ]);
}
