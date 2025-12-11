<?php if ($tipos_curso):  ?>
    <div class="contenedor-scroll-horizontal">
        <table class="registros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Tipo de Curso</th>
                        <th>Modulo del tipo de curso</th>
                        <?php if(es_admin() || es_extracurricular_activities_coordinator()|| es_complementary_credits_coordinator() || es_foreign_languages_coordinator()&& ($crear ?? false)):  ?>
                        <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody > <!-- Mostrar los resultados -->
                    <?php foreach( $tipos_curso as $tipo_curso): ?>
                    <tr>
                        <td> <?php echo $tipo_curso->id;?> </td>
                        <td class="nombre_curso"> <?php echo $tipo_curso->nombre_curso;?> </td>
                        <td class="modulo_nombre"> <?php echo $tipo_curso->nombre_modulo;?> </td>
                        <?php if(es_admin() || es_extracurricular_activities_coordinator()|| es_complementary_credits_coordinator() || es_foreign_languages_coordinator()&& ($crear ?? false)){  ?>
                        <td class="acciones-crud">
                            <a href="/tipos-curso-actualizar?id=<?php echo s($tipo_curso->id); ?>" 
                                class="boton-amarillo-block">Actualizar</a>
                            <form method="POST" action="/tipos-curso-eliminar" class="form-eliminar">
                                <input type="hidden" name="id" value="<?php echo s($tipo_curso->id); ?>">
                                <input type="hidden" name="tipo" value="tipos-curso">
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