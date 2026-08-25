<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from Types
 * 
 * @var string $title
 * @var Route[] $routes
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/fontawesome/css/all.min.css" rel="stylesheet">
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php require_once 'navbar.php'; ?>