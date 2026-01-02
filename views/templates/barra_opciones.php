
<div class="barra__opciones__paginacion">
<div class="barra-opciones">
    <?php if(es_admin() || es_extracurricular_activities_coordinator() && ($buscar ?? false)): ?>
        <a class="boton-verde-block" href="<?php echo $buscar;?>">Buscar registro</a>
    <?php endif; ?>
    <?php if(es_admin() || es_extracurricular_activities_coordinator() && isset($crear) && !empty($crear)): ?>
        <a class="boton-azul-block" href="<?php echo $crear;?>">Crear registro</a>
    <?php endif; ?>
</div>
<?php 
if ($sidebar_nav === 'Ingles' || $sidebar_nav === 'Cursos de Actividades Extraescolares' || $sidebar_nav === 'Configuracion Modulo') { ?>
    <div class="periodos-id">
        <form id="form-periodo" method="GET">
            <label for="periodo_id">Periodo:</label>
            <select name="periodo_id" id="periodo_id" onchange="this.form.submit()">
                <?php foreach ($periodos as $periodo): ?>
                    <option value="<?= $periodo->id ?>" <?= $periodo->id == $periodo_seleccionado ? 'selected' : '' ?>>
                        <?= $periodo->meses_Periodo ?> <?= $periodo->year ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="page" value="1">
        </form>
    </div>
<?php }?>
<div class="paginacion">
    <?php echo $paginacion ?? null; ?>
</div>

</div>
