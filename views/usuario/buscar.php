<?php  include_once __DIR__ . '/header-usuario.php'; ?>
<h1 class="nombre-pagina">Buscar Usuario</h1>
<p class="descripcion-pagina">Llena el formulario para buscar el registro</p>

<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php'; ?>
    <form class="formulario"    action="/aulas-buscar" method="POST">

    <div class="campo">
        <label for="columna">Columna:</label>
        <select name="columna" id="columna">
            <option value=""> --Seleccione una columna--</option>
            <option value="email"> Email </option>
            <option value="rol"> Rol </option>
            <option value="persona_id"> Matricula/Número de control </option>

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
<?php if ($aulas): ?>
    <table class="registros">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Aula</th>
                <?php if(es_admin()  && ($crear ?? false)): ?>
                <th>Acciones</th>
                <?php endif; ?>                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $aulas->id; ?></td>
                <td class="nombre_Aula"><?php echo $aulas->nombre_Aula; ?></td>
                <?php if (es_admin()  && ($crear ?? false)): ?>
                <td class="acciones-crud">
                    <a href="/aulas-actualizar?id=<?php echo s($aulas->id); ?>" 
                        class="boton-amarillo-block">Actualizar</a>
                    <form method="POST" action="/aulas-eliminar" class="form-eliminar">
                        <input type="hidden" name="id" value="<?php echo s($aulas->id); ?>">
                        <input type="hidden" name="tipo" value="concepto">
                        <input type="submit" class="boton-rojo-block" value="Eliminar">
                    </form>
                </td>
                <?php endif; ?>                
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php  include_once __DIR__ . '/footer-usuario.php'; ?>