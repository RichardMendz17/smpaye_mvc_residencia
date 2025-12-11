<?php if ($personal):  ?>
    <div class="contenedor-scroll-horizontal">
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
                        <div class="contorno-azul">
                            <td class="id"> <?php echo $persona->id;?> </td>
                            <td class="nombre"> <?php echo $persona->nombre;?> </td>
                            <td class="apellido_Paterno"> <?php echo $persona->apellido_Paterno;?> </td>
                            <td class="apellido_Materno"> <?php echo $persona->apellido_Materno;?> </td>
                            <td class="genero"> <?php echo $persona->genero;?> </td>
                            <?php if(es_admin() && ($crear ?? false)){  ?>
                            <td class="acciones-crud">
                                <a href="/personal-actualizar?id=<?php echo s($persona->id); ?>" 
                                    class="boton-amarillo-block">Actualizar</a>
                                <form method="POST" action="personal-eliminar" class="form-eliminar">
                                    <input type="hidden" name="id" value="<?php echo s($persona->id); ?>">
                                    <input type="hidden" name="tipo" value="concepto">
                                    <input type="submit" class="boton-rojo-block" value="Eliminar">
                                </form>
                            </td>
                        </div>
                        <?php } ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>
    </div>
<?php endif;?>