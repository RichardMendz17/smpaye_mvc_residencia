<div class="campo">
    <label for="Docente">Docente</label>
    <select id="Docente" name="curso[docente_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($docentes as $docente) { ?>
            <option 
                <?php echo $curso->docente_id === $docente->id ? 'selected' : ''; ?>
                value="<?php echo s($docente->id); ?>" >
                <?php echo s($docente->nombre_Docente ." ". $docente->apellido_Paterno ); ?> 
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
    <label for="Niveles">Niveles</label>
    <select id="Niveles" name="curso[nivel_id]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($niveles as $nivel) { ?>
            <option 
                <?php echo $curso->nivel_id === $nivel->id ? 'selected' : ''; ?>
                value="<?php echo s($nivel->id); ?>" >
                <?php echo s($nivel->nombre_Nivel ); ?> 
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