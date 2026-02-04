<?php

function make404(): void
{
    $title = 'Página não encontrada';

    makePage('404', [
        'title' => $title
    ]);
}