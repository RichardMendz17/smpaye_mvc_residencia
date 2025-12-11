<div class="campo campo_registro">
    <label for="matricula">Número de Matricula</label>
    <input 
        type="text"
        id="matricula"
        placeholder="matricula"
        name="personal[id]"
        value="<?php  echo s($personal->id); ?>"
    />
</div>

<div class="campo">
    <label for="nombre">Nombre</label>
    <input 
        type="text"
        id="nombre"
        placeholder="Nombre"
        name="personal[nombre]"
        value="<?php  echo s($personal->nombre); ?>"
    />
</div>

<div class="campo">
    <label for="apellidoPaterno">Apellido Paterno</label>
    <input 
        type="text"
        id="apellidoPaterno"
        placeholder="Apellido Paterno"
        name="personal[apellido_Paterno]"
        value="<?php  echo s($personal->apellido_Paterno); ?>"
    />
</div>

<div class="campo">
    <label for="apellidoMaterno">Apellido Materno</label>
    <input 
        type="text"
        id="apellidoMaterno"
        placeholder="Apellido Materno"
        name="personal[apellido_Materno]"
        value="<?php  echo s($personal->apellido_Materno); ?>"
    />
</div>

<div class="campo">
    <label for="genero">Genero</label>
    <select name="personal[genero]" id="genero">
        <option value=""> --Seleccione--</option>
        <option <?php echo ($personal->genero === 'Masculino') ? 'selected' : '' ?> value="Masculino">Masculino</option>
        <option <?php echo ($personal->genero === 'Femenino') ? 'selected' : '' ?>value="Femenino">Femenino</option>
    </select>
</div>