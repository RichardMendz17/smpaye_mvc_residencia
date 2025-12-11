<?php  include_once __DIR__ . '/header-tipos-curso.php' ?>
<h1 class="nombre-pagina">Buscar Nivel</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>

<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>
    <form class="formulario"    action="/tipos-curso-buscar" method="POST">
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
<?php if ($tipos_curso): ?>
    <table class="registros">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Tipo de Curso</th>
                <th>Modulo</th>
            <?php if (es_admin() && ($crear ?? false)): ?>
                <th>Acciones</th>
            <?php endif; ?>                
            </tr>
        </thead>
    <tbody>
        <?php foreach ($tipos_curso as $tipo_curso): ?>
            <tr>
                <td><?php echo $tipo_curso->id; ?></td>
                <td class="nombre_curso"><?php echo $tipo_curso->nombre_curso; ?></td>
                <?php if (es_admin() && ($crear ?? false)): ?>
                    <td class="acciones-crud">
                        <a href="/tipos-curso-actualizar?id=<?php echo s($tipo_curso->id); ?>" 
                            class="boton-amarillo-block">Actualizar</a>
                        <form method="POST" action="/tipos-curso-eliminar" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($tipo_curso->id); ?>">
                            <input type="hidden" name="tipo" value="nivel">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
<?php endif; ?>

<?php  include_once __DIR__ . '/footer-tipos-curso.php' ?>