<div class="campo">
    <label for="rol">Rol/Tipo de usuario</label>
    <select id="rol" name="personal_rol[id_rol]">
        <option value=""> --Seleccione--</option>
        <?php foreach ($roles as $rol) { ?>
            <option 
                value="<?php echo s($rol->id); ?>" >
                <?php echo s($rol->rol); ?> 
            </option>
        <?php } ?>
    </select>
</div>
<div class="campo">
    <label>ID de la persona</label>
    <input
        type="number"
        id="persona_id"
        placeholder="Escribe al menos 4 digitos..."
        name="usuario[persona_id]"
        autocomplete="off"
    />
</div> 
<div class="contenedor-sugerencias-alumnos">
    <div id="sugerencias-alumnos" class="sugerencias"></div>
</div>
<div class="campo campo-automatico">
    <label for="email">Email</label>
    <input 
        type="email"
        id="email"
        placeholder="Email automático"
        name="usuario[email]"
        readonly       
        >
</div>
<div class="campo campo-automatico">
    <label for="password">Password</label>
    <input 
        type="text"
        id="password"
        placeholder="Contraseña automático"
        name="usuario[password]"
        readonly>
</div>
