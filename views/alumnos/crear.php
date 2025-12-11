<?php  include_once __DIR__ . '/header-alumno.php' ?>
<h1 class="nombre-pagina">Nuevo Alumno</h1>
<p class="descripcion-pagina">Llena el formulario para crear un registro</p>

<div class="contenedor-sm">
    <a class="boton-azul-block" href="<?php echo $crear_varios;?>">Importar Alumnos</a>

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>
    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Guardar Alumno">
    </form>

</div>


<?php  include_once __DIR__ . '/footer-alumno.php' ?>