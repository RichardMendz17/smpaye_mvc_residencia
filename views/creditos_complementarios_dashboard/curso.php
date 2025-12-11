<?php  include_once __DIR__ . '/header-dashboard.php'; ?>
    <div class="contenedor-95">
            <div class="detalles-curso">
                <div class="informacion-curso">
                <table class="registro-curso">
                        <thead>
                            <tr>
                                <th>Docente</th>
                                <th>Nivel</th>
                                <th>Periodo</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody > <!-- Mostrar los resultados -->
                            <tr>
                                <td class="nombre_docente"> <?php echo $curso->nombre_docente; ?> </td>
                                <td class="nombre_Nivel"> <?php echo $curso->nombre_Nivel; ?> </td>
                                <td class="periodo"> <?php echo $curso->periodo; ?> </td>
                                <td class="nombre_Aula"> <?php echo $curso->nombre_Aula; ?> </td>
                            </tr>
                        </tbody>
                </table>
                </div>
                <?php if(es_admin()){ ?>
                <div class="curso-dropdown">
                    <button class="config-btn">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="dropdown-content">
                        <a class="editar-curso" href="/cursos-actualizar?id=<?php echo s($curso->id); ?>">Actualizar</a>
                        <form method="POST" action="/cursos-eliminar" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($curso->id); ?>">
                            <input type="hidden" name="tipo" value="curso">
                            <button type="submit" class="eliminar-curso">Eliminar</button>
                        </form>
                    </div>
                </div>
                <?php }?>

            </div>
    </div>

    <div class="contenedor-95">
        <div class="curso-opciones">
    <?php if(es_admin() || es_teacher()):?>        
            <div id="filtros" class="filtros">
                <div class="filtros-inputs">
                    <h2>Filtros:</h2>
                    <div class="campo-filtros">
                        <label for="todos">Todos</label>
                        <input 
                            type="radio"
                            id="todos"
                            name="filtro"
                            value=""
                            checked
                        />
                    </div>                
                    <div class="campo-filtros">
                        <label for="inscritos">Inscritos</label>
                        <input 
                            type="radio"
                            id="inscritos"
                            name="filtro"
                            value="inscrito"
                        />
                    </div>   
                    <div class="campo-filtros">
                        <label for="aprobados">Aprobados</label>
                        <input 
                            type="radio"
                            id="aprobados"
                            name="filtro"
                            value="aprobado"
                        />
                    </div>

                    <div class="campo-filtros">
                        <label for="reprobados">Reprobados</label>
                        <input 
                            type="radio"
                            id="reprobados"
                            name="filtro"
                            value="reprobado"
                        />
                    </div>

                    <div class="campo-filtros">
                        <label for="retirados">Retirados</label>
                        <input 
                            type="radio"
                            id="retirados"
                            name="filtro"
                            value="retirado"
                        />
                    </div>                
                </div>
            </div>
    <?php endif; ?>
            <div class="opciones-curso">
                <?php if(es_admin()): ?>                
                <div class="contenedor-agregar-alumnos">
                    <button
                        type="button"
                        class="agregar-alumnos"
                        id="agregar-alumnos"
                    >&#43; Agregar alumnos </button>
                </div>
                <?php endif; ?>                
                <div class="contenedor-agregar-horario">
                    <button
                        type="button"
                        class="agregar-horario"
                        id="agregar-horario"
                    >
                    </button>
                </div>
                <?php if (es_student()): ?>
                <div class="contenedor-obtener-boleta">
                    <button
                        type="button"
                        class="obtener-boleta"
                        id="obtener-boleta"
                    >
                    </button>
                </div>    
                <?php endif;?>              
            </div>
        </div>                 
    </div>

<div class="contenedor-95">
    <div class="contenedor-scroll-horizontal">
        <ul id="listado-alumnos" class="listado-alumnos"></ul>
    </div>
</div>    


<?php  include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

if (es_admin()) {
    $script .= '<script src="build/js/alumnos_curso_admin.js"></script>';
    $script .= '<script src="build/js/horario_curso_admin.js"></script>';
} elseif (es_teacher()) {
    $script .= '<script src="build/js/alumnos_curso_docente.js"></script>';
    $script .= '<script src="build/js/horario_curso.js"></script>';

} elseif (es_student()) {
    $script .= '<script src="build/js/alumnos_curso_alumno.js"></script>';
    $script .= '<script src="build/js/horario_curso.js"></script>';
    $script .= '<script src="build/js/boleta_alumno.js"></script>';
}
// career_manager u otros roles no cargan script extra
?>
