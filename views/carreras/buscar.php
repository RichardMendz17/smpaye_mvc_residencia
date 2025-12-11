<?php  include_once __DIR__ . '/header-carrera.php' ?>
<h1 class="nombre-pagina">Buscar Carrera</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>

<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>
    <form class="formulario"    action="/carreras-buscar" method="POST">

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
<?php if ($carreras): ?>
    <table class="registros">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de la carrera</th>
                <?php if(es_admin() && ($crear ?? false)): ?>
                <th>Acciones</th>
                <?php endif; ?>                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $carreras->id; ?></td>
                <td class="nombre_Carrera"><?php echo $carreras->nombre_Carrera; ?></td>
                <?php if (es_admin() && ($crear ?? false)): ?>
                <td class="acciones-crud">
                    <a href="/carreras-actualizar?id=<?php echo s($carreras->id); ?>" 
                        class="boton-amarillo-block">Actualizar</a>
                    <form method="POST" action="/carreras-eliminar" class="form-eliminar">
                        <input type="hidden" name="id" value="<?php echo s($carreras->id); ?>">
                        <input type="hidden" name="tipo" value="concepto">
                        <input type="submit" class="boton-rojo-block" value="Eliminar">
                    </form>
                </td>
                <?php endif; ?>                
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php  include_once __DIR__ . '/footer-carrera.php' ?>