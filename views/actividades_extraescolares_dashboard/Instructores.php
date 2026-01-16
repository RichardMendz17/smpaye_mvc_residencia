<?php  include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php' ?>

<p class="descripcion-pagina">El administrador del sistema es quien puede gestionar a los instructores</p>

<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/alertas.php'; 
?>
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
                        </div>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>
    </div>
<?php endif;?>

<?php  include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php' ?>



