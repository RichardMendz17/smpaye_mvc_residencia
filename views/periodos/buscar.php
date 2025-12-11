<?php  include_once __DIR__ . '/header-periodos.php' ?>

<h1 class="nombre-pagina">Buscar Periodo</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php';   ?>
    <form class="formulario" action="/periodos-buscar" method="GET">

    <div class="campo">
        <label for="columna">Columna:</label>
        <select name="columna" id="columna">
            <option value=""> --Seleccione una columna--</option>
            <?php foreach ($columnasDB as $columna) { 
                // Extraer solo el nombre de columna sin prefijo
                $partes = explode('.', $columna);
                $nombreLegible = end($partes); // toma la última parte (el nombre puro)
            ?>
                <option 
                    <?php echo $columna_Seleccionada === $columna ? 'selected' : ''; ?>
                    value="<?php echo s($columna); ?>">
                    <?php echo s(ucwords(str_replace('_', ' ', $nombreLegible))); ?>
                </option>
            <?php } ?>
        </select>
        </div>

    <div class="campo">
        <label for="dato">Dato</label>
        <input  type="text" 
                name="dato" 
                id="dato" 
                value="<?php echo s($registro); ?>"
                >
    </div>
        <button type="submit" class="boton-azul-flex">Buscar</button>
    </form>
</div>
<?php if ($periodos): ?>
<table class="registros">
    <thead>
        <tr>
            <th>ID</th>
            <th>Meses del Periodo</th>
            <th>Año</th>
            <?php if (es_admin() && ($crear ?? false)): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($periodos as $periodo): ?>
            <tr>
                <td><?php echo $periodo->id; ?></td>
                <td class="meses"><?php echo $periodo->meses_Periodo; ?></td>
                <td class="year"><?php echo $periodo->year; ?></td>
                <?php if (es_admin() && ($crear ?? false)): ?>
                    <td class="acciones-crud">
                        <a href="/periodos-actualizar?id=<?php echo s($periodo->id); ?>" 
                            class="boton-amarillo-block">Actualizar</a>
                        <form method="POST" action="/periodos-eliminar" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($periodo->id); ?>">
                            <input type="hidden" name="tipo" value="periodo">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

<div class="barra__opciones__paginacion alinear-derecha">
    <?php
        echo $paginacion ?? null; 
    ?>
</div>
<?php  include_once __DIR__ . '/footer-periodos.php' ?>