
<?php  include_once __DIR__ . '/header-calificaciones.php' ?>
<p class="descripcion-pagina">Para consultar las calificaciones de un alumno introduzca su número de control</p>



<?php
  
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';


?>
<div class="contenedor-sm">
    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>

    <form action="/calificaciones-alumno" method="POST">
        <div class="campo">
            <label for="num_Control">Número de control</label>
            <input 
                type="number"
                id="num_Control"
                placeholder="Número Control"
                name="num_Control_Alumno"
                value="<?php echo isset($_SESSION['num_Control_Alumno']) ? s($_SESSION['num_Control_Alumno']) : ''; ?>"
            />
        </div>
        <button type="submit" class="boton-azul-flex">Buscar</button>
    </form>
</div>


<?php if ($calificacion_alumno): ?>
    <table class="registros">
        <thead>
            <tr>
                <th>Número de control</th>
                <th>Nombre del Alumno</th>
                <th>Curso</th>
                <th>Nombre del Docente</th>
                <th>Periodo</th>
                <th>Calificación</th>
                <th>Estatus</th>
                <th>Fecha inscripción</th>
            </tr>
        </thead>
        <tbody > <!-- Mostrar los resultados -->
            <?php foreach( $calificacion_alumno as $calificacion): ?>
            <tr>
                <td> <?php echo $calificacion->numero_control;?> </td>
                <td> <?php echo $calificacion->nombre_Alumno;?> </td>
                <td> <?php echo $calificacion->curso;?> </td>
                <td> <?php echo $calificacion->nombre_Docente;?> </td>
                <td> <?php echo $calificacion->periodo;?> </td>
                <td> <?php echo $calificacion->calificacion ?? 'Aún sin asignar'; ?> </td>
                <td> <?php echo $calificacion->estatus;?> </td>
                <td> <?php echo $calificacion->fecha_inscripcion;?> </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>


<?php  include_once __DIR__ . '/footer-calificaciones.php' ?>

