<?php  include_once __DIR__ . '/header-dashboard.php' ?>

<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

    <p class="text-center"> Bienvenido: <?php echo $_SESSION['nombre'] ?></p>

<?php  include_once __DIR__ . '/footer-dashboard.php' ?>