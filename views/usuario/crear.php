<?php  include_once __DIR__ . '/header-usuario.php'; ?>
<h1 class="nombre-pagina">Crear usuario</h1>
<p class="descripcion-pagina">Introduce los datos completos para crear un usuario</p>

<div class="contenedor-sm ">
        
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

            <form class="formulario" method="POST" action="/usuarios-crear" >
                <?php include_once __DIR__ . '/formulario.php'; ?>
                <input type="submit" class="boton" value="Crear Usuario">
            </form>
</div>

<?php  include_once __DIR__ . '/footer-usuario.php'; ?>

<?php
$script .= '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/crear_usuario.js"></script>
';
?>

