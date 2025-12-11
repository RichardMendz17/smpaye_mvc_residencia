<?php if ($archivos_usuarios):  ?>
    <div class="contenedor-scroll-horizontal">
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
                        <td> <img src="/build/img/excel.avif" alt="Excel" width="50" style="vertical-align: middle;"></td>
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
    </div>
<?php endif;?>