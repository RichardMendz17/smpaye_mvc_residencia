<?php
$tipos = [
    'mensaje_exito' => ['titulo' => '¡Éxito!', 'tipo' => 'success'],
    'mensaje_error' => ['titulo' => '¡Error!', 'tipo' => 'error']
];

foreach ($tipos as $clave => $config)
{
    if (!empty($_SESSION[$clave]))
    {
        $mensaje = $_SESSION[$clave];
        unset($_SESSION[$clave]);

        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo "<script>
            Swal.fire(" . json_encode($config['titulo']) . ", " . json_encode($mensaje) . ", " . json_encode($config['tipo']) . ");
        </script>";
    }
}
?>


<?php 
    foreach ($alertas as $key => $mensajes): 
        foreach ($mensajes as $mensaje):
?>
    <div class="alerta <?php echo $key; ?>"><?php echo $mensaje; ?></div>
<?php 
        endforeach;
    endforeach; 
?>