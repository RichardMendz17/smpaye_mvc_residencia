<?php  include_once __DIR__ . '/header-dashboard.php' ?>
<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>    
    <?php if( count($cursos_ingles) === 0){?>
        <?php if(es_admin()):?>
            <p class="no-"> No hay cursos aún...
                <a href="/crear-curso">Comienza creando uno</a> 
            </p>
        <?php else:?>
            <p class="no-"> Usted no tiene cursos asignados en este periodo </p>
        <?php endif;?>         
    <?php   } else {?>
            <div class="listado-cursos">
                <?php foreach ($cursos_ingles as $curso) { ?>
                    <a href="/curso-ingles?id=<?php echo $curso->curso_url ?>">
                        <div class="curso">
                            <p><strong>Docente:</strong> <?php echo $curso->nombre_docente; ?></p>
                            <p><strong>Nivel:</strong> <?php echo $curso->nombre_Nivel; ?></p>
                            <p><strong>Periodo:</strong> <?php echo $curso->periodo; ?></p>
                            <p><strong>Aula:</strong> <?php echo $curso->nombre_Aula; ?></p>
                        </div>
                    </a>
                <?php } ?>
            </div>
            <?php }?>
<?php  include_once __DIR__ . '/footer-dashboard.php' ?>