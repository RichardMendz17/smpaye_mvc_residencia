<?php  include_once __DIR__ . '/header-seleccionar-rol.php'; ?>
<h1 class="nombre-pagina">Crear usuario</h1>
<p class="descripcion-pagina">Introduce los datos completos para asignar un nuevo rol a un personal</p>

<div class="contenedor-sm ">

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

            <form class="formulario" method="POST" action="/asignacion-roles-crear" >
                <?php include_once __DIR__ . '/formulario.php'; ?>
                <input type="submit" class="boton" value="Asignar Rol">
            </form>
</div>

<?php  include_once __DIR__ . '/footer-seleccionar-rol.php'; ?>

<?php
$script .= '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/asignacion_rol.js"></script>
';
?>

