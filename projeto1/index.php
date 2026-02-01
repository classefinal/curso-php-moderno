<?php

$title = 'Curso de PHP - ' . date('d/m/Y H:i:s');

$age = 18;
$tabuada = 7;
$imgAdulto = "https://tse2.mm.bing.net/th/id/OIP.-4O6ZaA8fUZaVI9os31GVQHaE7?rs=1&pid=ImgDetMain&o=7&rm=3";
$imgMenor = "https://blog.mag.com.br/wp-content/uploads/2023/11/beneficiario-menor-de-idade-mag.jpeg";

// echo '
// <!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>' . $title . '</title>
// </head>
// <body>';

// for ($i = 0; $i < 10; $i++) {
//     echo "<p>$i x $tabuada = " . $i * $tabuada . "</p>";
// }

// if ($age < 18) {
//     echo '<img src="' . $imgMenor . '" />';
// } else {
//     echo '<img src="' . $imgAdulto . '" />';
// }

// echo '</body>
// </html>
// ';
?>

<?php require_once 'header.php' ?>

<body>
    <?php for ($i = 0; $i < 10; $i++): ?>
        <?php if ($i % 2 === 0): ?>
            <?php require 'strong_p.php' ?>
        <?php else: ?>
            <?php require 'italic_p.php' ?>
        <?php endif ?>
    <?php endfor ?>

    <?php if ($age < 18): ?>
        <img src="<?= $imgMenor ?>" />
    <?php else: ?>
        <img src="<?= $imgAdulto ?>" />
    <?php endif ?>

    
<?php require_once 'footer.php' ?>