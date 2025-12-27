<?php  include_once __DIR__ . '/header-alumno.php' ?>
<h1 class="nombre-pagina">Importar Alumnos</h1>
<p class="descripcion-pagina">Seleccione el archivo para importar alumnos</p>

<div class="contenedor-sm">
    <div>
        <a class="boton-azul-block" href="<?php echo $crear;?>">Crear Alumno</a>
    </div>

    <?php   include_once __DIR__ . '/../templates/alertas.php';   ?>
      <form method="POST" enctype="multipart/form-data"/>
        <div class="centrar-contenido">
            <input  type="file" name="dataCursos" id="file-input" required/>
            <label for="file-input"><span>. Elegir Archivo Excel</span></label >
        </div>

          <input type="submit" name="subir" class="boton-rojo-flex" value="Importar"/>

      </form>

</div>


<?php  include_once __DIR__ . '/footer-alumno.php' ?>