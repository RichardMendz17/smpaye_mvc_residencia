<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>
    <p class="descripcion-pagina">Administración de cursos</p>
<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>    
    <?php if( count($cursos_actividades_extraescolares) === 0){?>
        <?php if(es_admin()):?>
            <p class="no-"> No hay cursos aún...
                <a href="/crear-curso">Comienza creando uno</a> 
            </p>
        <?php else:?>
            <p class="no-"> Usted no tiene cursos asignados en este periodo </p>
        <?php endif;?>         
    <?php   } else {?>
            <div class="listado-cursos">
                <?php foreach ($cursos_actividades_extraescolares as $curso) {
                       // debuguear($curso);
                    ?>
                    <a href="/curso-actividades-extraescolares?id=<?php echo $curso->curso_url ?>">
                        <div class="curso">
                            <p><strong>Instructor:</strong> <?php echo $curso->nombre_encargado; ?></p>
                            <p><strong>Actividad Extraescolar:</strong> <?php echo $curso->nombre_curso; ?></p>
                            <p><strong>Periodo:</strong> <?php echo $curso->periodo; ?></p>
                            <p><strong>Aula:</strong> <?php echo $curso->nombre_Aula; ?></p>
                            <p><strong>Inscripción libre:</strong> <?php echo $curso->inscripcion_alumno; ?></p>
                        </div>
                    </a>
                <?php } ?>
            </div>
            <?php }?>
<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>