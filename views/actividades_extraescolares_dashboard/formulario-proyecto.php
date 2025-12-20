<div class="campo">
    <label for="Instructor">Instructor</label>
    <select id="Instructor" name="curso[encargado_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($encargados as $encargado) { ?>
            <option 
                <?php echo $curso->encargado_id === $encargado->id ? 'selected' : '';?>
                value="<?php echo s($encargado->id); ?>" >
                <?php echo s($encargado->nombre ." ". $encargado->apellido_Paterno ); ?> 
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
    <input type="checkbox" id="inscripcion" name="curso[inscripcion_alumno]">
    <input type="hidden" id="inscripcion_valor" name="curso[inscripcion_alumno]">
</div>
<hr>
<div class="campo-number">
    <label for="limite_alumnos">Establecer un limite de cupos para el curso</label>
    <input type="checkbox" id="limite_alumnos">
    <label for="cantidad_limite_alumnos">Cantidad Limite de Cupos:</label>
    <input type="number" id="cantidad_limite_alumnos" placeholder="Ej: 30">
    <input type="hidden" id="cantidad_final" name="curso[limite_alumnos]">
</div>
<hr>
<div class="campo-number">
    <input type="hidden" name="curso_requisitos[minimo_aprobados]" value="Null">
    <label for="cursos_necesarios">Establecer una cantidad de cursos aprobados requeridos para ingresar al curso actual</label>
    <input type="checkbox" id="cursos_necesarios" onclick="document.getElementById('cantidad_cursos_necesarios').disabled = !this.checked">
    <label for="cantidad_cursos_necesarios">Cantidad de Cursos Necesarios:</label><input type="number" id="cantidad_cursos_necesarios" name="curso_requisitos[minimo_aprobados]" disabled>
</div>
<hr>
<input type="hidden" name="curso[estado]" value="Creado">
