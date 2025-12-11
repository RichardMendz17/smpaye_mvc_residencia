<?php

namespace Controllers;

use MVC\Router;
use Model\Aulas;
use Model\Cursos_Ingles;
use Model\Niveles;
use Model\Personal_Coordinacion_Lenguas_Extranjeras;
use Model\Periodos;
use Classes\Paginacion;
use Model\AlumnoCursoDetalles;
use Model\BitacoraEventos;
use Model\CursosDetalles;

class InglesDashboardController 
{
    public static function index(Router $router)
    {
        isAuth();
        $rol = $_SESSION['rol'];
        $alertas = [];
        $persona_id = $_SESSION['persona_id'];

        // Obtenemos parámetros de filtrado
        $periodo_id = $_GET['periodo_id'] ?? null;

        // Redirigir inmediatamente si no hay periodo seleccionado
        if ($periodo_id === false || $periodo_id === null)
        {
            switch ($rol)
            {
                case 0: // Admin
                    $periodo_reciente = Periodos::SQL("SELECT id FROM periodos ORDER BY year DESC, meses_Periodo DESC LIMIT 1");
                break;
                case 1: // Docente
                    $periodo_reciente = Periodos::SQL("
                        SELECT periodos.id
                        FROM periodos
                        JOIN cursos_ingles ON cursos.periodo_id = periodos.id
                        WHERE cursos.docente_id = {$persona_id}
                        GROUP BY periodos.id
                        ORDER BY periodos.year DESC, periodos.meses_Periodo DESC
                        LIMIT 1
                    ");
                break;
                case 2: // Alumno
                    $periodo_reciente = Periodos::SQL("
                        SELECT periodos.id
                        FROM periodos
                        JOIN cursos_ingles ON cursos_ingles.periodo_id = periodos.id
                        JOIN alumno_curso_detalles ON cursos_ingles.id = alumno_curso_detalles.curso_detalle_id
                        WHERE alumno_curso_detalles.alumno_id = {$persona_id}
                        GROUP BY periodos.id
                        ORDER BY periodos.year DESC, periodos.meses_Periodo DESC
                        LIMIT 1
                    ");

                break;
            }

            $periodo_id = $periodo_reciente[0]->id ?? 1;
            header("Location: /ingles-dashboard?periodo_id={$periodo_id}&page=1");
            exit;
        }

        $periodo_filtro = "&periodo_id={$periodo_id}";
        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        // Validación de página
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header("Location: /ingles-dashboard?periodo_id={$periodo_id}&page=1");
            exit;
        }
        
        $registros_por_pagina = 6;
        
        // Para un admin nos traemos todos los registros de periodos
        $periodos = Periodos::SQL( "SELECT id, meses_Periodo, year FROM periodos ORDER BY year DESC, meses_Periodo DESC" );

        // Construimos la consulta base
        $query_base = "SELECT DISTINCT cursos_ingles.id, cursos_ingles.url AS curso_url,
            CONCAT(docentes.nombre_Docente, ' ',docentes.apellido_Paterno, ' ', docentes.apellido_Materno) AS nombre_docente,
            niveles.nombre_Nivel as nombre_Nivel,
            CONCAT(periodos.meses_Periodo, ' ', periodos.year) AS periodo,
            periodos.meses_Periodo, periodos.year,
            aulas.nombre_Aula as nombre_Aula,
            periodos.id as periodo_id
            FROM cursos_ingles 
            LEFT OUTER JOIN docentes ON cursos_ingles.docente_id = docentes.id 
            LEFT OUTER JOIN niveles ON cursos_ingles.nivel_id = niveles.id 
            LEFT OUTER JOIN periodos ON cursos_ingles.periodo_id = periodos.id 
            LEFT OUTER JOIN aulas ON cursos_ingles.aula_id = aulas.id
            LEFT OUTER JOIN alumno_curso_detalles ON cursos_ingles.id = alumno_curso_detalles.curso_detalle_id";

        // Añadimos condiciones según rol para la consulta de cursos y periodos
        $where = '';
        switch ($rol)
        {
            case 0: 
                //No hace falta agregar condiciones a la consulta base ni en los periodos para el admin
            break; // Admin
            case 1:
                $where = " WHERE docente_id = {$persona_id} ";
                //Buscamos solo los periodos donde el docente tiene cursos
                $periodos = Periodos::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos_ingles
                JOIN periodos ON cursos_ingles.periodo_id = periodos.id
                WHERE cursos_ingles.docente_id = {$persona_id}
                ORDER BY periodos.year DESC, periodos.meses_Periodo DESC");
            break; // Docente
            case 2: 
                $where = " WHERE alumno_curso_detalles.alumno_id = {$persona_id} ";
                //Buscamos solo los periodos donde el alumno tiene cursos_ingles
                $periodos = Periodos::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos_ingles
                JOIN periodos ON cursos_ingles.periodo_id = periodos.id
                JOIN alumno_curso_detalles ON cursos_ingles.id = alumno_curso_detalles.curso_detalle_id
                WHERE alumno_curso_detalles.alumno_id = {$persona_id}
                ORDER BY periodos.year DESC, periodos.meses_Periodo DESC");                
            break; // Alumno
        }
        // Aplicamos filtro de periodo (obligatorio)
        $where .= ($where ? " AND " : " WHERE ") . " periodos.id = {$periodo_id} ";

        // Consulta para contar registros
        $query_conteo = $query_base . $where;
        $total_registros = CursosDetalles::SQLContar($query_conteo);
        // Paginación
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total_registros, $periodo_filtro);

        // Consulta paginada
        $query = $query_base . $where . " ORDER BY periodos.year DESC, periodos.meses_Periodo DESC ";
        $query .= " LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";
        //debuguear($query);
        $cursos_ingles = CursosDetalles::SQL($query);

        // Obtenemos periodos disponibles
        //debuguear($cursos);
        $router->render('ingles_dashboard/index', [
            'titulo_pagina' => 'Cursos de Inglés',
            'sidebar_nav' => 'Ingles',
            'alertas'=>$alertas,        
            'cursos_ingles' => $cursos_ingles,
            'paginacion' => $paginacion->paginacion(),
            'periodos' => $periodos,
            'periodo_seleccionado' => $periodo_id
        ]);
    }

    public static function crear_curso(Router $router)
    {
        $curso_ingles = new Cursos_Ingles;
        isAuth();
        $alertas = [];
        $docentes = Docentes::all();
        $aulas = Aulas::all();
        $niveles = Niveles::all();
        $periodos = Periodos::all();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $curso_ingles = new Cursos_Ingles($_POST['curso_ingles']);
            $curso_ingles->sincronizar($_POST);
            //Validación
            $alertas = $curso_ingles->validarCurso();
            if (empty($alertas)) 
            {
                // Generar una URL única
                $hash = md5(uniqid());
                $curso_ingles->url = $hash;
                // Guardar el proyecto
                $curso_ingles->guardar();
                $id_registro = $curso_ingles->id;
                if ($curso_ingles)
                {
                    $tabla = 'Cursos_Ingles';
                    $evento = new BitacoraEventos;
                    $evento->eventos(1, $id_registro, $tabla);
                    header('Location: /curso?id=' . $curso_ingles->url);
                    exit; // OBLIGATORIO para que se haga la redirección correctamente
                } 
                // Redireccionar
            }
        }

        $router->render('ingles_dashboard/crear-curso', [
        'titulo_pagina' => 'Cursos',
        'sidebar_nav' => 'Cursos',  
            'alertas' => $alertas,
            'docentes'=> $docentes,
            'aulas' => $aulas,
            'niveles' => $niveles,
            'periodos' => $periodos,
            'curso_ingles' => $curso_ingles
        ]);
    }

    public static function curso(Router $router)
    {
        isAuth();
        $pagina_actual = $_GET['id'] ?? 1;
        $pagina_actual = s($pagina_actual);
        $persona_id = $_SESSION['persona_id'];
        if (!$pagina_actual)
        {
            header('Location: /dashboard');
        }
        $curso_ingles = Cursos_Ingles::where('url', $pagina_actual);
            if(!$curso_ingles){
            header('Location: /dashboard');
            exit;
        }
        if ($_SESSION['rol'] === 1 && $curso_ingles->docente_id !== $_SESSION['persona_id']) 
        {
            header('Location: /dashboard');
            exit;
        }
        if ($_SESSION['rol'] === 2) 
        {
            $id_curso = $curso_ingles->id;
            $query = "SELECT * FROM alumno_curso_detalles AS acd 
            WHERE acd.curso_detalle_id = {$id_curso} 
            AND acd.alumno_id={$_SESSION['persona_id']}";
            $curso_ingles = AlumnoCursoDetalles::unicoSQL($query);
            if (!$curso_ingles->num_rows)
            {
                header('Location: /dashboard');
                exit;
            }

        }            
        $query = "SELECT curso_ingles.id, curso_ingles.url AS curso_url,
        CONCAT(docentes.nombre_Docente, ' ',docentes.apellido_Paterno, ' ', docentes.apellido_Materno) AS nombre_docente,
        niveles.nombre_Nivel as nombre_Nivel,
        CONCAT (periodos.meses_Periodo, ' ', periodos.year) AS periodo,
        aulas.nombre_Aula as nombre_Aula ";
        $query .= " FROM curso_ingles LEFT OUTER JOIN docentes ";
        $query .= " ON curso_ingles.docente_id = docentes.id ";
        $query .= " LEFT OUTER JOIN niveles ";
        $query .= " ON curso_ingles.nivel_id = niveles.id ";
        $query .= " LEFT OUTER JOIN periodos ";
        $query .= " ON curso_ingles.periodo_id = periodos.id ";
        $query .= " LEFT OUTER JOIN aulas ";
        $query .= " ON curso_ingles.aula_id = aulas.id ";            
        $query .= " WHERE curso_ingles.url = '{$pagina_actual}'";            
        $query .= " ORDER BY periodos.year DESC ";
        
        $curso = CursosDetalles::obtenerUnico($query);
        $router->render('ingles_dashboard/curso', [
        'titulo_pagina' => 'Curso de Inglés',
        'sidebar_nav' => 'Ingles',            
            'curso' => $curso,
            'titulo' => 'Curso'
        ]);
    }

    public static function eliminar_Curso()
    {
        $id = validarORedireccionar('/ingles_dashboard');
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //Validar id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if(!campoVacio($id))
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo)) 
                {
                    $curso = Cursos_Ingles::find($id);
                    $resultado =  $curso->eliminar();
                    if (!campoVacio($resultado))
                    {
                        $tabla = 'curso_ingles';
                        $id_curso = $curso->id;
                        if (!campoVacio($id_curso))
                        {
                            $_SESSION['mensaje_exito'] = 'El curso fue eliminado correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(3, $id_curso, $tabla);
                            header("Location: /ingles_dashboard");
                            exit;
                        }                          
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el curso, probablemente tenga alumno(s) asignado(s).';
                        header("Location: /ingles_dashboard");
                        exit;
                        }
                }
            }
        }
    }
    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/ingles_dashboard');
        $alertas = [];
        isAuth();
        $curso_ingles = Cursos_Ingles::find($id);
        $docentes = Docentes::all();
        $aulas = Aulas::all();
        $niveles = Niveles::all();
        $periodos = Periodos::all();
        if ($curso_ingles == false)
        {
            header("Location: /ingles_dashboard");
        }
        $alertas = Cursos_Ingles::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $args = $_POST['curso'];
            $curso->sincronizar($args);
            $alertas = $curso->validarCurso();
            if(empty($alertas))
            {
                    $curso->actualizar();
                    $tabla = 'cursos';
                    $id_curso = $curso->id;
                    if (campoVacio($id_curso))
                    {
                        $_SESSION['mensaje_error'] = 'El curso no se actualizo.';
                        header('Location: /ingles_dashboard');
                        //header('Location: /curso-buscar?columna=docentes.id&dato=' . $curso->id);
                        exit;
                    } else  
                        {
                            $_SESSION['mensaje_exito'] = 'El curso fue actualizado correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(2, $id_curso, $tabla);
                            header('Location: /ingles_dashboard');
                            //header('Location: /curso-buscar?columna=docentes.id&dato=' . $curso->id);
                            exit;
                        }
            }

        }
        $alertas = Docentes::getAlertas();
        $router->render('/ingles_dashboard/actualizar',[
            'curso'=>$curso,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Curso',
            'sidebar_nav' => 'Cursos',
            'docentes'=> $docentes,
            'aulas' => $aulas,
            'niveles' => $niveles,
            'periodos' => $periodos,
        ]);
    }


}
?>