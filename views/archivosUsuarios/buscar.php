<?php  include_once __DIR__ . '/header-archivos-usuarios.php'; ?>
<h1 class="nombre-pagina">Buscar Archivo de Usuario</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>

<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>
    <form class="formulario" action="/archivos-usuarios-buscar" method="POST">

        <div class="campo">
            <label for="columna">Rol:</label>
            <select name="columna" id="columna">
                <option value=""> --Seleccione una columna--</option>
                <option value="Admin">Administrador</option>
                <option value="Teacher">Docente</option>
                <option value="Student">Estudiante</option>
                <option value="Career_manager">Jefe de Carrera</option>
            </select>
        </div>

        <div class="campo">
            <label for="dato">Número de Control/Matrícula</label>
            <input  type="text" 
                    name="dato" 
                    id="dato"
                    >
        </div>
        <button type="submit" class="boton-azul-flex">Buscar</button>
    </form>
</div>
<?php if ($archivos_usuarios): ?>
    <table class="registros">
            <thead>
                <tr>
                    <th>Formato</th>
                    <th>Nombre del archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody > <!-- Mostrar los resultados -->
                <?php foreach( $archivos_usuarios as $archivo): ?>
                <tr>
                    <td> <img src="/build/img/excel.avif" alt="Excel" width="50" style="vertical-align: middle;"> Excel </td>
                    <td class="nombre_Aula"> <?= s($archivo) ?></td>
                    <?php if(es_admin()  && ($crear ?? false)){  ?>
                    <td class="acciones-crud">
                        <a class="boton-amarillo-block" href="/archivos-usuarios-descargar?archivo=<?= urlencode($archivo) ?>" download>Descargar</a>
                        <form method="POST" action="archivos-usuarios-eliminar" class="form-eliminar">
                            <input type="hidden" name="archivo" value="<?= s($archivo) ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                    </td>
                    <?php } ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
<?php endif; ?>

<?php  include_once __DIR__ . '/footer-archivos-usuarios.php'; ?>