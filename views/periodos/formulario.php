
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

