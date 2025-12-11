<?php  include_once __DIR__ . '/header-personal.php' ?>

<p class="descripcion-pagina">Administración de Personal</p>

<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<?php 
    include_once __DIR__ . '/resultados.php';
?>

<?php  include_once __DIR__ . '/footer-personal.php' ?>




