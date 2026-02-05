<?php

function makeNotFound(): void
{
    makePage('not_found', [
        'title' => 'Página não encontrada'
    ]);
}
