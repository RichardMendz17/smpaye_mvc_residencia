<?php  include_once __DIR__ . '/header-periodos.php' ?>

<h1 class="nombre-pagina">Actualizar Periodo</h1>
<p class="descripcion-pagina">Rellena los datos correctamente</p>


<div class="contenedor-sm">
    <?php    include_once __DIR__ . '/../templates/alertas.php'; ?>    
    <form method="POST" class="formulario form-actualizar">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Actualizar Periodo">
    </form>
</div>
<?php  include_once __DIR__ . '/footer-periodos.php' ?>