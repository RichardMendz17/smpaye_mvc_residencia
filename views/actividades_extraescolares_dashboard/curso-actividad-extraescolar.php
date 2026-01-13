<?php  
    include_once __DIR__ . '/header-actividades-extraescolares-dashboard.php';
    include_once __DIR__ . '/../templates/alertas.php'; 
?>
    <p class="descripcion-pagina">Datos del curso</p>
    <div class="contenedor-95">
            <div class="detalles-curso">
                <div class="informacion-curso">
                <table class="registro-curso">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Actividad <br> Extraescolar</th>
                                <th>Periodo</th>
                                <th>Aula</th>
                                <th>Registro <br> por el alumno</th>
                                <th>Limite de  <br> Alumnos</th>
                                <th>Cursos requeridos <br> para ingresar </th>
                                <th>Estado del <br> Curso</th>
                            </tr>
                        </thead>
                        <tbody > <!-- Mostrar los resultados -->
                            <tr>
                                <td class="nombre_instructor"> <?php echo $actividad_extraescolar_curso->nombre_encargado; ?> </td>
                                <td class="nombre_Nivel"> <?php echo $actividad_extraescolar_curso->nombre_curso; ?> </td>
                                <td class="periodo"> <?php echo $actividad_extraescolar_curso->periodo; ?> </td>
                                <td class="nombre_Aula"> <?php echo $actividad_extraescolar_curso->nombre_Aula; ?> </td>
                                
                                <td class="inscripcion_alumno"> <?php  echo $actividad_extraescolar_curso->inscripcion_alumno ?> </td>
                                <td class="limite_alumnos"> 
                                <?php  
                                    $limite = ($actividad_extraescolar_curso->limite_alumnos != null)  ? $actividad_extraescolar_curso->limite_alumnos
                                    : "Sin límite";
                                    echo $limite;
                                ?> 
                                </td>
                                <td class="cantidad_cursos_requeridos"> 
                                <?php  
                                    $cantidad_cursos_requeridos = ($curso_requisitos != null)  ? $curso_requisitos->minimo_aprobados
                                    : "No asignados";
                                    echo $cantidad_cursos_requeridos;
                                ?> 
                                </td>
                                <td class="curso_estado"> 
                                    <span class="estado_actual">
                                        <?php echo $actividad_extraescolar_curso->estado; ?>
                                    </span>
                                <?php if(es_extracurricular_activities_coordinator()){ ?>
                                    <div class="curso-dropdown">
                                        <button class="config-btn-estado">
                                            <i class="fa-solid fa-sliders"></i>
                                        </button>
                                        <div class="dropdown-content">
                                            <form method="POST" action="/curso-actualizar-estado-actividades-extraescolares" class="form-estado">
                                                <input type="hidden" name="id" value="<?php echo s($actividad_extraescolar_curso->id); ?>">
                                                <input type="hidden" name="tipo" value="curso">
                                                <input type="hidden" name="curso[estado]" class="estado-hidden">
                                                <button type="submit" name="curso[estado]" class="estado-curso" value="Creado" >Creado</button>
                                                <button type="submit"  name="curso[estado]"  class="estado-curso" value="Abierto" >Abierto</button>
                                                <button type="submit"  name="curso[estado]"  class="estado-curso" value="Cerrado" >Cerrado</button>
                                                <button type="submit"  name="curso[estado]"  class="estado-curso" value="Suspendido" >Suspendido</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                </table>
                </div>
                <?php if(es_extracurricular_activities_coordinator()){ ?>
                <div class="curso-dropdown">
                    <button class="config-btn">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="dropdown-content">
                        <a class="editar-curso" href="/curso-actualizar-actividades-extraescolares?id=<?php echo s($actividad_extraescolar_curso->id); ?>">Actualizar</a>
                        <form method="POST" action="/curso-eliminar-actividades-extraescolares" class="form-eliminar">
                            <input type="hidden" name="id" value="<?php echo s($actividad_extraescolar_curso->id); ?>">
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
    <?php if(es_extracurricular_activities_coordinator() || es_extracurricular_activities_instructor()):?>        
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
                <?php if(es_extracurricular_activities_coordinator()): ?>                
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


<?php
    include_once __DIR__ . '/footer-actividades-extraescolares-dashboard.php';
?>

<?php

$script .= '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

if (es_extracurricular_activities_coordinator()) 
{
    $script .= '<script src="build/js/alumnos_curso_admin.js"></script>';
    $script .= '<script src="build/js/horario_curso_admin.js"></script>';
} 
else if (es_extracurricular_activities_instructor()) 
{ //alumnos_curso_instructor_actividad_extraescolar
    $script .= '<script src="build/js/alumnos_curso_docente.js"></script>';
    $script .= '<script src="build/js/horario_curso.js"></script>';

} 
else if (es_student())
{
    $script .= '<script src="build/js/alumnos_curso_alumno.js"></script>';
    $script .= '<script src="build/js/horario_curso.js"></script>';
    $script .= '<script src="build/js/boleta_alumno.js"></script>';
}
// career_manager u otros roles no cargan script extra
?>
