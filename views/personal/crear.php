<?php  include_once __DIR__ . '/header-personal.php' ?>
<h1 class="nombre-pagina">Nuevo Personal</h1>
<p class="descripcion-pagina">Llena el formulario para crear un registro</p>

<div class="contenedor-sm">

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>

    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Crear Personal">
    </form>

</div>


<?php  include_once __DIR__ . '/footer-personal.php' ?>