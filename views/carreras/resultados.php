<?php if ($carreras):  ?>
<div class="contenedor-scroll-horizontal">
    <table class="registros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la carrera</th>
                    <?php if(es_admin() && ($crear ?? false)): ?>
                    <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody > <!-- Mostrar los resultados -->
                <?php foreach( $carreras as $carrera): ?>
                <tr>
                    <td> <?php echo $carrera->id;?> </td>
                    <td class="nombre_Carrera"> <?php echo $carrera->nombre_Carrera;?> </td>
                    <?php if(es_admin() && ($crear ?? false)){  ?>
                    <td class="acciones-crud">
                        <a href="carreras-actualizar?id=<?php echo s($carrera->id); ?>" 
                            class="boton-amarillo-block">Actualizar</a>
                        <form method="POST" action="carreras-eliminar" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($carrera->id); ?>">
                            <input type="hidden" name="tipo" value="carrera">
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