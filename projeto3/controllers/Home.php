<?php

function makeHome(): void
{
    $title = 'Home';

    makePage('home', [
        'title' => $title
    ]);
}
