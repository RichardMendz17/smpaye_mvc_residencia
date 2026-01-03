<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>
    <p class="descripcion-pagina">Configuracion de modulo por periodo activo</p>

<div class="contenedor-sm">
    <p>La Configuracion corresponde al periodo activo de:</p>

    <?php 
        include_once __DIR__ . '/../templates/alertas.php'; 
        include_once __DIR__ . '/../templates/barra_opciones.php';
    ?>

    <form class="formulario" method="POST" action="/configuracion-modulo-actividades-extraescolares">
        <div class="campo-number">
            <label for="limite_cursos_por_periodo">Asignar una cantidad de cursos limite a los que el alumno puede inscribirse</label>
            <input 
                type="checkbox"
                id="limite_cursos_por_periodo"
                <?php
                    echo !empty($configuracion_modulo_periodo->maximo_cursos_por_periodo) && (int)$configuracion_modulo_periodo->maximo_cursos_por_periodo > 0 ? 'checked' : '';
                ?>
            >

            <label for="cantidad_limite_cursos">Cantidad Limite de Cursos para registrarse:</label>
            <input 
                type="number" 
                id="cantidad_limite_cursos" 
                placeholder="Ej: 30"
                <?php
                    echo !empty($configuracion_modulo_periodo->maximo_cursos_por_periodo) && (int)$configuracion_modulo_periodo->maximo_cursos_por_periodo ? 'value=' . $configuracion_modulo_periodo->maximo_cursos_por_periodo : '0';
                ?>
            >
            <input type="hidden" id="cantidad_final_cursos_periodo" name="configuracion_modulo_periodo[maximo_cursos_por_periodo]">
        </div>
    <hr>    
        <div class="campo-number">
            <label for="fecha_limite_inscripcion_checkbox">Asignar una  fecha limite para que el alumno pueda inscribirse a algun curso</label>
            <input 
                    type="checkbox"
                    id="fecha_limite_inscripcion_checkbox"
                <?php
                    echo !empty($configuracion_modulo_periodo->fecha_limite_inscripcion) ? 'checked' : '';
                ?>
            >

            <label for="fecha_limite_inscripcion">Fecha Limite para registrarse en cursos:</label>
            <input 
                class="input-date"
                type="date" 
                id="fecha_limite_inscripcion" 
                placeholder="Ej: 30"
                <?php
                    echo !empty($configuracion_modulo_periodo->fecha_limite_inscripcion) ? 'value=' . $configuracion_modulo_periodo->fecha_limite_inscripcion : '0';
                ?>
            >
            <input type="hidden" id="fecha_limite_inscripcion_final" name="configuracion_modulo_periodo[fecha_limite_inscripcion]">
        </div>
    <hr>
        <?php if(es_admin() || es_extracurricular_activities_coordinator()):?>
            <input type="submit" value="Guardar Cambios">
        <?php endif;?>
    </form>
</div>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>
<?php 
    $script .=    $script = '<script src="build/js/configuracion_modulo.js"></script>';
?>
