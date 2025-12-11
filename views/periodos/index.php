<?php  include_once __DIR__ . '/header-periodos.php' ?>
<p class="descripcion-pagina">Administración de Periodos</p>

<?php
  
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php';


?>

<?php 
include_once __DIR__ . '/resultados.php';
?>

<?php  include_once __DIR__ . '/footer-periodos.php' ?>