<?php  include_once __DIR__ . '/header-alumno.php'; ?>
<h1 class="nombre-pagina">Buscar Alumno</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>
<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>    
    <form class="formulario" action="/alumnos-buscar" method="GET">
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

<?php if ($alumnos): ?>
    <table class="registros">
        <thead>
            <tr>
                <th>Número de control</th>
                <th>Carrera</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Teléfono</th>
                <th>Comentarios</th>
                <th>Correo institucional</th>
                <th>Genero</th>
                <?php if(es_admin() && ($crear ?? false)): ?>
                <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $alumno): ?>
            <tr>
                <td class="numero_control  text-sm"> <?php echo $alumno->id;?> </td>
                <td class="nombre_Carrera  text-sm"> <?php echo $alumno->nombre_Carrera;?> </td>
                <td class="nombre_Alumno  text-sm"> <?php echo $alumno->nombre_Alumno;?> </td>
                <td class="apellido_Paterno  text-sm"> <?php echo $alumno->apellido_Paterno;?> </td>
                <td class="apellido_Materno  text-sm"> <?php echo $alumno->apellido_Materno;?> </td>
                <td class="text-sm"> <?php echo $alumno->telefono;?> </td>
                <td class="text-sm"> <?php echo $alumno->comentarios; ?></td>
                <td class="text-sm"> <?php echo $alumno->correo_institucional;?> </td>
                <td class="text-sm"> <?php echo $alumno->genero;?> </td>
                <?php if (es_admin()  && ($crear ?? false)): ?>
                <td class="acciones-crud">
                    <a href="/alumnos-actualizar?id=<?php echo s($alumno->id); ?>" 
                        class="boton-amarillo-block">Actualizar</a>
                    <form method="POST" action="/alumnos-eliminar" class="form-eliminar">
                        <input type="hidden" name="id" value="<?php echo s($alumno->id); ?>">
                        <input type="hidden" name="tipo" value="concepto">
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
    <?php echo $paginacion ?? null; ?>
</div>
<?php  include_once __DIR__ . '/footer-alumno.php'; ?>