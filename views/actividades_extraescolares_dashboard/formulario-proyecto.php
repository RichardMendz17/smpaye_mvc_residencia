<div class="campo">
    <label for="Instructor">Instructor</label>
    <select id="Instructor" name="curso[encargado_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($personal as $persona) { ?>
            <option 
                <?php echo $curso->encargado_id === $persona->id ? 'selected' : '';?>
                value="<?php echo s($persona->id); ?>" >
                <?php echo s($persona->nombre ." ". $persona->apellido_Paterno ." ". $persona->apellido_Materno); ?> 
            </option>
        <?php } ?>
    </select>
</div>

<div class="campo">
    <label for="Aulas">Aulas</label>
    <select id="Aulas" name="curso[aula_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($aulas as $aula) { ?>
            <option 
                <?php echo $curso->aula_id === $aula->id ? 'selected' : ''; ?>
                value="<?php echo s($aula->id); ?>" >
                <?php echo s($aula->nombre_Aula); ?> 
            </option>
        <?php } ?>
    </select>
</div>

<div class="campo">
    <label for="Actividad_Extraescolar">Tipo de Actividad Extraescolar</label>
    <select id="Actividad_Extraescolar" name="curso[tipo_curso_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($tipos_curso as $tipo_curso) { ?>
            <option 
                <?php echo $curso->tipo_curso_id === $tipo_curso->id ? 'selected' : ''; ?>
                value="<?php echo s($tipo_curso->id); ?>" >
                <?php echo s($tipo_curso->nombre_curso ); ?> 
            </option>
        <?php } ?>
    </select>
</div>

<div class="campo">
    <label for="Periodo">Periodos</label>
    <select id="Periodo" name="curso[periodo_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($periodos as $periodo) { ?>
            <option 
                <?php echo $curso->periodo_id === $periodo->id ? 'selected' : ''; ?>
                value="<?php echo s($periodo->id); ?>" >
                <?php echo s($periodo->meses_Periodo ." ". $periodo->year ); ?> 
            </option>
        <?php } ?>
    </select>
</div>
<hr>
<div class="campo-checkbox">
    <label for="inscripcion">Permitir que los alumnos se inscriban</label>
    <input 
            type="checkbox"
            id="inscripcion"
            name="curso[inscripcion_alumno]" 
            <?php echo $curso->inscripcion_alumno == 'Permitido' ? 'checked' : '';?> 
    >
    <input type="hidden" id="inscripcion_valor" name="curso[inscripcion_alumno]">
</div>
<hr>
<div class="campo-number">
    <label for="limite_alumnos">Establecer un limite de cupos para el curso</label>
    <input 
            type="checkbox"
            id="limite_alumnos"
        <?php
            echo !empty($curso->limite_alumnos) && (int)$curso->limite_alumnos > 0 ? 'checked' : '';
        ?>
    >

    <label for="cantidad_limite_alumnos">Cantidad Limite de Cupos:</label>
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
    <label for="cursos_necesarios">Establecer una cantidad de cursos aprobados requeridos para ingresar al curso actual</label>
    <input 
        type="checkbox"
        id="cursos_necesarios"
        <?php
            echo !empty($curso_requisitos->minimo_aprobados) ? 'checked' : '';
        ?>
    >
    <label for="cantidad_cursos_necesarios">Cantidad de Cursos Necesarios:</label>
    <input 
        type="number" 
        id="cantidad_cursos_necesarios" 
        placeholder="Ej:2"
        <?php
        
            echo !campoVacio($curso_requisitos) && $curso_requisitos->id_curso == $curso->id ? 'value=' .$curso_requisitos->minimo_aprobados : '';
        ?>
    >
    <input type="hidden" id="curso_requisitos" name="curso[requisitos]">
    <input type="hidden" id="cantidad_final_cursos_necesarios" name="curso_requisitos[minimo_aprobados]">
    <input type="hidden" id="cantidad_final_cursos_necesarios" name="curso_requisitos[id_periodo]">
</div>
<hr>
<input type="hidden" name="curso[estado]" value="Creado">
