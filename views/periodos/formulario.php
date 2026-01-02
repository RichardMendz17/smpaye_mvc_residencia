
<div class="campo">
    <label for="meses">Meses del periodo</label>
    <input 
        type="text"
        id="meses"
        placeholder="Meses del periodo"
        name="periodo[meses_Periodo]"
        value="<?php  echo s($periodo->meses_Periodo); ?>"
    />
</div>

<div class="campo">
    <label for="año">Año del periodo</label>
    <input 
        type="number"
        id="año"
        placeholder="Año del periodo"
        name="periodo[year]"
        value="<?php  echo s($periodo->year); ?>"
    />
</div>

<div class="campo">
    <label for="estado">Estado</label>
    <select name="periodo[estado]" id="estado">
        <option value=""> --Seleccione--</option>
        <option <?php echo ($periodo->estado === 'Activo') ? 'selected' : '' ?> value="Activo">Activo</option>
        <option <?php echo ($periodo->estado === 'Suspendido') ? 'selected' : '' ?>value="Suspendido">Suspendido</option>
        <option <?php echo ($periodo->estado === 'Cerrado') ? 'selected' : '' ?>value="Cerrado">Cerrado</option>
    </select>
</div>

<div class="campo">
    <label for="fecha_inicio">Fecha inicio del periodo</label>
    <input
        type="date"
        id="fecha_inicio"
        name="periodo[fecha_inicio]"
        value="<?php echo s($periodo->fecha_inicio); ?>"
    />
</div>

<div class="campo">
    <label for="fecha_fin">Fecha fin del periodo</label>
    <input
        type="date"
        id="fecha_fin"
        name="periodo[fecha_fin]"
        value="<?php echo s($periodo->fecha_fin); ?>"
    />
</div>
