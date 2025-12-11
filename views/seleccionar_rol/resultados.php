<?php if ($usuario_roles):  ?>
    <div class="contenedor-scroll-horizontal contenedor">
        <form method="POST" action="/seleccionar-rol" class="form-seleccionar-rol">
        <table class="registros">
            <thead>
                <tr>
                    <th>Nombre del Rol</th>
                    <th>Define un rol predeterminado</th>
                    <th>Elige un rol para esta sesion</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuario_roles as $usuario_rol): ?>
                <tr>
                    <td class="nombre_rol">
                        <?php echo $usuario_rol->nombre_rol; ?>
                        <?php if (isset($_SESSION['rol_por_defecto'])) 
                        {
                        echo ($usuario_rol->id_rol == $_SESSION['rol_por_defecto']) ? '[Predeterminado]' : '';
                        } ?>
                    </td>
                    <td>
                        <?php
                        if (isset($_SESSION['rol_por_defecto']) && $usuario_rol->id_rol == $_SESSION['rol_por_defecto']) 
                        {
                          
                        ?>
                        <button 
                            type="submit" 
                            name="rol[quitar_id_rol_predeterminado]" 
                            value="<?php echo $usuario_rol->id_rol; ?>" 
                            class="boton-rojo-flex"
                        > Quitar rol predeterminado
                        </button>
                        <?php 
                            } 
                         else  {
                        ?>
                        <button 
                            type="submit" 
                            name="rol[id_rol_predeterminado]" 
                            value="<?php echo $usuario_rol->id_rol; ?>" 
                            class="boton-azul-flex "
                        >
                            Colocar por predeterminado
                        </button>
                        <?php       } ?>
                    </td>
                    <td>
                        <button 
                            type="submit" 
                            name="rol[id_rol]" 
                            value="<?php echo $usuario_rol->id_rol; ?>"
                            class="boton-amarillo-flex"> Selecciona este rol
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <input type="hidden" name="rol[id_personal]" value="<?php echo $usuario_roles[0]->id_personal; ?>">
        </form>
    </div>
<?php endif;?>

