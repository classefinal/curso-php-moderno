<?php

require_once 'functions.php';

define('COMPONENTS', getComponentsPath());

$products = [
    [
        'name' => 'Café',
        'unit' => 'Kg',
        'quantity' => 10
    ],
    [
        'name' => 'Sabão liquido',
        'unit' => 'L',
        'quantity' => 5
    ],
    [
        'name' => 'Arroz',
        'unit' => 'Kg',
        'quantity' => 3
    ]
];

$data = [
    'title' => 'Lista de produtos',
    'products' => $products,
    'searchUrl' => 'https://google.com/search'
];

makePage($data);