<?php
    include_once __DIR__ . '/header-seleccionar-rol.php';
?>

<p class="descripcion-pagina">Administración de Roles</p>



<?php 
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';

?>
<div class="contenedor-sm">
     <?php    include_once __DIR__ . '/../templates/alertas.php'; ?>
</div>
<?php 
    include_once __DIR__ . '/resultados.php';
?>

<?php  
    include_once __DIR__ . '/footer-seleccionar-rol.php';
?>