<?php  include_once __DIR__ . '/header-tipos-curso.php' ?>

<p class="descripcion-pagina">Administración de Tipos de cursos</p>
<?php
    $resultado = $_GET['resultado']  ?? NULL;
    include_once __DIR__ . '/../templates/barra_opciones.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<?php include_once __DIR__ . '/resultados.php'; ?>
<?php  include_once __DIR__ . '/footer-tipos-curso.php'; ?>