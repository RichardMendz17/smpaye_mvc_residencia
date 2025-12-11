<?php  include_once __DIR__ . '/header-carrera.php' ?>
<h1 class="nombre-pagina">Nueva Carrera</h1>
<p class="descripcion-pagina">Introduce el nombre de la nueva carrera para crearla</p>

<div class="contenedor-sm">

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>

    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Guardar Carrera">
    </form>

</div>

<?php  include_once __DIR__ . '/footer-carrera.php' ?>