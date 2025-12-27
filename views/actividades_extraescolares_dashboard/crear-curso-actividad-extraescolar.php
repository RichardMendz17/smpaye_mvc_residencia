<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>

    <div class="contenedor-sm">
        <?php include_once __DIR__  .'/../templates/alertas.php'; ?>
        <a class="boton-azul-block" href="<?php echo $crear_varios;?>">Importar cursos</a>

        <form class="formulario" method="POST" action="/crear-curso-actividades-extraescolares" >

            <?php  include_once __DIR__ . '/formulario-proyecto.php'; ?>
            <input type="submit" value="Crear Curso Actividades Extraescolares">
        </form>
    </div>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>

<?php
    $script .= '<script src="build/js/requisitos_curso.js"></script>';
?>