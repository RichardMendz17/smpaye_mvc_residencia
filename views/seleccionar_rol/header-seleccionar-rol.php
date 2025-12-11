<div class="dashboard">
    <?php  include_once __DIR__ . '/../templates/sidebar.php' ?>

    <div class="principal">
        <?php include_once __DIR__ . '/../templates/barra.php';?>

        <div class="contenido">
            <h2 class="nombre-pagina"><?php echo $sidebar_nav; ?></h2>
<?php
    $crear = '/asignacion-roles-crear';
    $buscar = '/asignacion-roles-buscar';
?>