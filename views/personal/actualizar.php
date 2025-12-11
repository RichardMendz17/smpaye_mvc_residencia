<?php  include_once __DIR__ . '/header-personal.php' ?>

<h1 class="nombre-pagina">Actualizar Personal</h1>
<p class="descripcion-pagina">Actualiza los datos</p>

<div class="contenedor-sm">
    <form method="POST" class="formulario form-actualizar"  >
        <?php include_once __DIR__ . '/formulario.php'; ?>
        <input type="submit" class="boton-azul-flex" value="Actualizar Personal">
    </form>
</div>

<?php  include_once __DIR__ . '/footer-personal.php' ?>