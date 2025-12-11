<?php if ($aulas):  ?>
    <div class="contenedor-scroll-horizontal">
        <table class="registros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del aula</th>
                        <?php if(es_admin()  && ($crear ?? false)): ?>
                        <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody > <!-- Mostrar los resultados -->
                    <?php foreach( $aulas as $aula): ?>
                    <tr>
                        <td> <?php echo $aula->id;?> </td>
                        <td class="nombre_Aula"> <?php echo $aula->nombre_Aula;?> </td>
                        <?php if(es_admin()  && ($crear ?? false)){  ?>
                        <td class="acciones-crud">
                            <a href="aulas-actualizar?id=<?php echo s($aula->id); ?>" 
                                class="boton-amarillo-block">Actualizar</a>
                            <form method="POST" action="aulas-eliminar" class="form-eliminar">
                                <input type="hidden" name="id" value="<?php echo s($aula->id); ?>">
                                <input type="hidden" name="tipo" value="aula">
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