<?php  include_once __DIR__ . '/header-tipos-curso.php'; ?>

<h1 class="nombre-pagina">Actualizar Tipo de Actividad</h1>
<p class="descripcion-pagina">Actualiza los datos</p>

<div class="contenedor-sm">
    <?php    include_once __DIR__ . '/../templates/alertas.php'; ?>       
    <form method="POST" class="formulario form-actualizar">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Actualizar Tipo de Curso">
    </form>
</div>

<?php  include_once __DIR__ . '/footer-tipos-curso.php' ?>