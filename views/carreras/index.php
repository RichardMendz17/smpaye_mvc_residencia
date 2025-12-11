<?php  include_once __DIR__ . '/header-carrera.php' ?>

<p class="descripcion-pagina">Administración de Carreras</p>

<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php';
?>

<?php 
include_once __DIR__ . '/resultados.php';
?>

<?php  include_once __DIR__ . '/footer-carrera.php' ?>