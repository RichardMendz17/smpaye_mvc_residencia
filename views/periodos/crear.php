<?php  include_once __DIR__ . '/header-periodos.php' ?>

<p class="descripcion-pagina">Introduce los meses y año del nuevo periodo</p>


<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form method="POST" class="formulario">
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Guardar Periodo">
    </form>
</div>
<?php  include_once __DIR__ . '/footer-periodos.php' ?>