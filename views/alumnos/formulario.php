
<div class="campo campo_registro">
    <label for="numero_control">Número de control</label>
    <input 
        type="number"
        id="numero_control"
        placeholder="Número de control"
        name="alumno[id]"
        value="<?php  echo s($alumno->id); ?>"
    />
</div>

<div class="campo">
    <label for="id_Carrera">Carrera</label>
    <select id="Carrera" name="alumno[id_Carrera]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($carreras as $carrera) { ?>
            <option 
                <?php echo $alumno->id_Carrera === $carrera->id ? 'selected' : ''; ?>
                value="<?php echo s($carrera->id); ?>" >
                <?php echo s($carrera->nombre_Carrera); ?> 
            </option>
        <?php } ?>
    </select>
</div>

<div class="campo">
    <label for="nombre">Nombre del Alumno</label>
    <input 
        type="text"
        id="nombre"
        placeholder="Nombre del alumno"
        name="alumno[nombre_Alumno]"
        value="<?php  echo s($alumno->nombre_Alumno); ?>"
    />
</div>

<div class="campo">
    <label for="apellidoPaterno">Apellido Paterno</label>
    <input 
        type="text"
        id="apellidoPaterno"
        placeholder="Apellido Paterno"
        name="alumno[apellido_Paterno]"
        value="<?php  echo s($alumno->apellido_Paterno); ?>"
    />
</div>

<div class="campo">
    <label for="apellidoMaterno">Apellido Materno</label>
    <input 
        type="text"
        id="apellidoMaterno"
        placeholder="Apellido Materno"
        name="alumno[apellido_Materno]"
        value="<?php  echo s($alumno->apellido_Materno); ?>"
    />
</div>

<div class="campo">
    <label for="telefono">Telefono</label>
    <input 
        type="text"
        id="telefono"
        placeholder="Telefono del alumno"
        name="alumno[telefono]"
        value="<?php  echo s($alumno->telefono); ?>"
    />
</div>

<div class="campo">
    <label for="comentarios">Comentarios</label>
    <input 
        type="text"
        id="comentarios"
        placeholder="Comentarios"
        name="alumno[comentarios]"
        value="<?php  echo s($alumno->comentarios); ?>"
    />
</div>

<div class="campo">
    <label for="correo_institucional">Correo institucional</label>
    <input 
        type="text"
        id="correo_institucional"
        placeholder="Correo institucional del alumno"
        name="alumno[correo_institucional]"
        value="<?php  echo s($alumno->correo_institucional); ?>"
    />
</div>

<div class="campo">
    <label for="genero">Genero</label>
    <select name="alumno[genero]" id="genero">
        <option value=""> --Seleccione--</option>
        <option <?php echo ($alumno->genero === 'Masculino') ? 'selected' : '' ?> value="Masculino">Masculino</option>
        <option <?php echo ($alumno->genero === 'Femenino') ? 'selected' : '' ?>value="Femenino">Femenino</option>
    </select>
</div>
