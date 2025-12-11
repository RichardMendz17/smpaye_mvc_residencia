<?php  include_once __DIR__ . '/header-aulas.php' ?>
<h1 class="nombre-pagina">Nueva Aula</h1>
<p class="descripcion-pagina">Introduce el nombre de la nueva aula para crearla</p>

<div class="contenedor-sm">

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>

    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Guardar Aula">
    </form>

</div>

<?php  include_once __DIR__ . '/footer-aulas.php' ?>