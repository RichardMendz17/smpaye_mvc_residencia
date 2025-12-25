<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>

<div class="contenedor-sm">

    <form method="POST" class="formulario form-actualizar"  >
        <?php include_once __DIR__ . '/formulario-proyecto.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Actualizar Curso">
    </form>

</div>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>

<?php
    $script .= '<script src="build/js/requisitos_curso.js"></script>';
?>