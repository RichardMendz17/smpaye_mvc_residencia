<?php if ($alumnos):  ?>
    <div class="contenedor-scroll-horizontal">
        <table class="registros">
                <thead>
                    <tr>
                        <th>Número <br> de control</th>
                        <th>Carrera</th>
                        <th>Nombre</th>
                        <th>Apellido <br> Paterno</th>
                        <th>Apellido <br> Materno</th>
                        <th>Teléfono</th>
                        <th>Comentarios</th>
                        <th>Correo institucional</th>
                        <th>Género</th>
                        <?php if(es_admin() && ($crear ?? false)): ?>
                        <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody > <!-- Mostrar los resultados -->
                    <?php foreach( $alumnosDetalles as $alumno): ?>
                    <tr>
                        <td class="numero_control text-sm "> <?php echo $alumno->id;?> </td>
                        <td class="nombre_Carrera text-sm"> <?php echo $alumno->nombre_Carrera;?> </td>
                        <td class="nombre_Alumno text-sm"> <?php echo $alumno->nombre_Alumno;?> </td>
                        <td class="apellido_Paterno text-sm"> <?php echo $alumno->apellido_Paterno;?> </td>
                        <td class="text-sm"> <?php echo $alumno->apellido_Materno;?> </td>
                        <td class="text-sm"> <?php echo $alumno->telefono;?> </td>
                        <td class="text-sm"> <?php echo $alumno->comentarios;?> </td>
                        <td class="text-sm"> <?php echo $alumno->correo_institucional;?> </td>
                        <td class="text-sm"> <?php echo $alumno->genero;?> </td>
                        <?php if(es_admin()  && ($crear ?? false)){  ?>
                        <td class="acciones-crud">
                            <a href="/alumnos-actualizar?id=<?php echo s($alumno->id); ?>" 
                                class="boton-amarillo-block">Actualizar</a>
                            <form method="POST" action="alumnos-eliminar" class="form-eliminar">
                                <input type="hidden" name="id" value="<?php echo s($alumno->id); ?>">
                                <input type="hidden" name="tipo" value="concepto">
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