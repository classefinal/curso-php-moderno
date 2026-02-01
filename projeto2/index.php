<?php

require_once 'functions.php';

$baseUrl = 'https://google.com/search';

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
    'title' => 'Curso de PHP moderno',
    'products' => $products,
    'baseUrl' => $baseUrl
];

var_dump($_GET);
var_dump($_POST);
var_dump($_SERVER);
makePage($data);