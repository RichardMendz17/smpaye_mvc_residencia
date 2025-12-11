<?php  include_once __DIR__ . '/header-personal.php' ?>
<h1 class="nombre-pagina">Buscar Personal</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>


<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>    
    <form class="formulario" action="/personal-buscar" method="GET">

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
        <input 
            type="text" 
            name="dato" 
            id="dato"
            value="<?php echo s($registro); ?>"
            >
    </div>
        <button type="submit" class="boton-azul-flex">Buscar</button>
    </form>
</div>

<?php if ($personal): ?>
<table class="registros">
        <thead>
            <tr>
                <th>Matrícula de trabajo</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Genero</th>
                <?php if(es_admin() && ($crear ?? false)): ?>
                <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody > <!-- Mostrar los resultados -->
            <?php foreach( $personal as $persona): ?>
            <tr>
                <td class="numero_control"> <?php echo $persona->id;?> </td>
                <td class="nombre"> <?php echo $persona->nombre;?> </td>
                <td class="apellido_Paterno"> <?php echo $persona->apellido_Paterno;?> </td>
                <td class="apellido_Materno"> <?php echo $persona->apellido_Materno;?> </td>
                <td class="apellido_Materno"> <?php echo $persona->genero;?> </td>
                <?php if(es_admin() && ($crear ?? false)){  ?>
                <td class="acciones-crud">
                    <a href="/personal-actualizar?id=<?php echo s($persona->id); ?>" 
                        class="boton-amarillo-block">Actualizar</a>
                    <form method="POST" action="persona-eliminar" class="form-eliminar">
                        <input type="hidden" name="id" value="<?php echo s($persona->id); ?>">
                        <input type="hidden" name="tipo" value="persona">
                        <input type="submit" class="boton-rojo-block" value="Eliminar">
                    </form>
                </td>
                <?php } ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>
<div class="barra__opciones__paginacion alinear-derecha">
    <?php
        echo $paginacion; 
    ?>
</div>
<?php  include_once __DIR__ . '/footer-personal.php' ?>