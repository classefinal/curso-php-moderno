<?php

/**
 * @return void
 */
function makeAbout(): void
{
    $title = 'Sobre';

    makePage('about', [
        'title' => $title
    ]);
}
