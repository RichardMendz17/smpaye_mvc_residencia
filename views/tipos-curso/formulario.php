<div class="campo">
    <label for="nombre">Nombre del Tipo de Actividad</label>
    <input 
        type="text"
        id="nombre"
        placeholder="Nombre del Curso"
        name="tipo_curso[nombre_curso]"
        value="<?php  echo s($tipo_curso->nombre_curso); ?>"
    />
</div>
<?php if (isset($modulo_id)) { ?>
    <input type="hidden" name="tipo_curso[modulo_id]" value="<?php echo s($modulo_id); ?>">
<?php } else {?>
    <div class="campo">
        <label for="Modulos">Modulo</label>
        <select id="Modulos" name="tipo_curso[modulo_id]">
            <option value=""> --Seleccione--</option>
            <?php foreach ($modulos as $modulo) { ?>
                <option 
                    <?php echo $tipo_curso->modulo_id === $modulo->id ? 'selected' : ''; ?>
                    value="<?php echo s($modulo->id); ?>" >
                    <?php echo s($modulo->nombre_modulo ); ?> 
                </option>
            <?php } ?>
        </select>
        <input type="hidden">
    </div>
<?php }?>