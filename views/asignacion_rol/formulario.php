<div class="campo">
    <label for="Rol">Rol/Tipo de usuario</label>
    <select id="Rol" name="asignacion_rol[id_rol]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($roles as $rol) { ?>
<!-- Aqui solo imprimimos roles diferentes de 1 correspondiente al rol de alumno -->
            <?php if ($rol->id != 1): ?>
                <option 
                    value="<?php echo s($rol->id); ?>" >
                    <?php echo s($rol->rol); ?> 
                </option>
            <?php endif; ?>
        <?php } ?>
    </select>
</div>
<div class="campo">
    <label>ID del personal</label>
    <input
        type="text"
        id="persona_id"
        placeholder="Escribe al menos 4 digitos..."
        name="asignacion_rol[id_personal]"
        autocomplete="off"
    />
</div> 
<div class="contenedor-sugerencias-alumnos">
    <div id="sugerencias-alumnos" class="sugerencias"></div>
</div>
<div class="campo campo-automatico">
    <label for="nombre_personal">Nombre del personal</label>
    <input 
        type="text"
        id="nombre_personal"
        placeholder="Nombre del personal [automático]"
        readonly       
        >
</div>
