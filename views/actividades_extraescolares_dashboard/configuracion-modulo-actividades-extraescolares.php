<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>

<div class="contenedor-sm">
    <p>La Configuracion corresponde al periodo activo de:</p>

    <?php 
        include_once __DIR__ . '/../templates/alertas.php'; 
        include_once __DIR__ . '/../templates/barra_opciones.php';
    ?>

    
    <form class="formulario" method="POST" action="/perfil">
        <div class="campo-number">
            <label for="limite_cursos">Asignar una cantidad de cursos limite a los que el alumno puede inscribirse</label>
            <input 
                    type="checkbox"
                    id="limite_cursos"
                <?php
                    echo !empty($periodo->limite_alumnos) && (int)$curso->limite_alumnos > 0 ? 'checked' : '';
                ?>
            >

            <label for="cantidad_limite_alumnos">Cantidad Limite de Cursos para registrarse:</label>
            <input 
                type="number" 
                id="cantidad_limite_alumnos" 
                placeholder="Ej: 30"
                <?php
                    echo !empty($curso->limite_alumnos) && (int)$curso->limite_alumnos ? 'value=' . $curso->limite_alumnos : '0';
                ?>
            >
            <input type="hidden" id="cantidad_final" name="curso[limite_alumnos]">
        </div>
        <hr>    
        <div class="campo-number">
            <label for="fecha_limite">Asignar una  fecha limite para que el alumno pueda inscribirse a algun curso</label>
            <input 
                    type="checkbox"
                    id="fecha_limite"
                <?php
                    echo !empty($curso->limite_alumnos) && (int)$curso->limite_alumnos > 0 ? 'checked' : '';
                ?>
            >

            <label for="cantidad_limite_alumnos">Fecha Limite para registrarse en cursos:</label>
            <input 
                type="number" 
                id="cantidad_limite_alumnos" 
                placeholder="Ej: 30"
                <?php
                    echo !empty($curso->limite_alumnos) && (int)$curso->limite_alumnos ? 'value=' . $curso->limite_alumnos : '0';
                ?>
            >
            <input type="hidden" id="cantidad_final" name="curso[limite_alumnos]">
        </div>
        <hr>
        <?php if(es_admin() || es_extracurricular_activities_coordinator()):?>
            <input type="submit" value="Guardar Cambios">
        <?php endif;?>
    </form>
</div>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>