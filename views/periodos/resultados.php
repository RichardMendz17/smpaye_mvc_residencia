<div class="contenedor-scroll-horizontal">
    <table class="registros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Periodos</th>
                    <th>Año</th>
                    <?php if(es_admin() && ($crear ?? false)): ?>
                    <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody > <!-- Mostrar los resultados -->
                <?php foreach( $periodos as $periodo): ?>
                <tr>
                    <td> <?php echo $periodo->id;?> </td>
                    <td class="meses"> <?php echo $periodo->meses_Periodo;?> </td>
                    <td class="year"> <?php echo $periodo->year;?> </td>
                    <?php if(es_admin() && ($crear ?? false)){  ?>
                    <td class="acciones-crud">
                        <a href="/periodos-actualizar?id=<?php echo s($periodo->id); ?>" 
                            class="boton-amarillo-block">Actualizar</a>
                        <form method="POST" action="periodos-eliminar" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($periodo->id); ?>">
                            <input type="hidden" name="tipo" value="periodo">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                    </td>
                    <?php } ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
</div>
