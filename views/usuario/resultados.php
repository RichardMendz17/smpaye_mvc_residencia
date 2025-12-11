<?php if ($usuariosDetalles):  ?>
    <div class="contenedor-scroll-horizontal">
        <table class="registros">
                <thead>
                    <tr>
                        <th>Correo</th>
                        <th>Tipo de Usuario</th>
                        <th>Matrícula/Numero de control</th>
                        <?php if(es_admin() && ($crear ?? false)): ?>
                        <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody > <!-- Mostrar los resultados -->
                    <?php foreach( $usuariosDetalles as $usuario): ?>
                    <tr>
                        <td class="email"> <?php echo$usuario->email; ?> </td>
                        <td class="persona_id"> <?php echo $resultado = ($usuario->tipo_usuario === '1') ? "Alumno" : (($usuario->tipo_usuario === '2') ? "Personal" : "Desconocido"); ?> </td>
                        <td class="persona_id"> <?php echo$usuario->persona_id; ?> </td>
                        <?php if(es_admin() && ($crear ?? false)){  ?>
                        <td class="acciones-crud">
                            <button 
                                id="cambiar_password"
                                class="boton-amarillo-block" 
                                data-id="<?php echo s($usuario->id); ?>"
                                >
                                Cambiar Contraseña
                            </button>
                            <form method="POST" action="/usuarios-eliminar" class="form-eliminar">
                                <input type="hidden" name="id" value="<?php echo s($usuario->id); ?>">
                                <input type="hidden" name="tipo" value="usuario">
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