<?php  include_once __DIR__ . '/header-tipos-curso.php'; ?>
<h1 class="nombre-pagina">Nueva Actividad</h1>
<p class="descripcion-pagina">Introduce el tipo de Actividad</p>

<div class="contenedor-sm">

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>

    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Guardar Tipo de Curso">
    </form>

</div>

<?php  include_once __DIR__ . '/footer-tipos-curso.php'; ?>