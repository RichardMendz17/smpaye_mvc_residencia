<?php if ($roles_personal):  ?>
    <div class="contenedor-scroll-horizontal">
        <table class="registros">
                <thead>
                    <tr>
                        <th>Id del Personal</th>
                        <th>Nombre del Personal</th>
                        <th>Nombre del Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody > <!-- Mostrar los resultados -->
                    <?php foreach( $roles_personal as $usuario_rol): ?>
                    <tr>
                        <td class="id_personal"> <?php echo $usuario_rol->id_personal;?> </td>
                        <td class="nombre_personal"> <?php echo $usuario_rol->nombre_personal;?> </td>
                        <td class="nombre_rol"> <?php echo $usuario_rol->nombre_rol;?> </td>
                        
                        <?php if(es_admin() && ($crear ?? false)){  ?>
                        <td class="acciones-crud">
                            <a href="/asignacion-roles-actualizar?id=<?php echo s($usuario_rol->id); ?>" 
                            class="boton-amarillo-block">Actualizar</a>
                            <form method="POST" action="/asignacion-roles-eliminar" class="form-eliminar">
                                <input type="hidden" name="id" value="<?php echo s($usuario_rol->id); ?>">
                                <input type="hidden" name="tipo" value="rol_personal">
                                <input type="submit" class="boton-rojo-block" value="Eliminar">
                            </form>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>
    </div>
<?php endif;?>