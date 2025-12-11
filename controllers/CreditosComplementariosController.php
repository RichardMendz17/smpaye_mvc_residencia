<?php

namespace Controllers;

use MVC\Router;
use Model\Aulas;
use Model\Cursos;
use Model\Niveles;
use Model\Usuario;
use Model\Docentes;
use Model\Periodos;
use Classes\Paginacion;
use Model\AlumnoCursoDetalles;
use Model\BitacoraEventos;
use Model\CursosDetalles;

class CreditosComplementariosDashboardController 
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
    if ($periodo_id === false || $periodo_id === null) {
        switch ($rol) {
            case 0: // Admin
                $periodo_reciente = Periodos::SQL("SELECT id FROM periodos ORDER BY year DESC, meses_Periodo DESC LIMIT 1");
            break;
            case 1: // Docente
                $periodo_reciente = Periodos::SQL("
                    SELECT periodos.id
                    FROM periodos
                    JOIN cursos ON cursos.periodo_id = periodos.id
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
                    JOIN cursos ON cursos.periodo_id = periodos.id
                    JOIN alumno_curso_detalles ON cursos.id = alumno_curso_detalles.curso_detalle_id
                    WHERE alumno_curso_detalles.alumno_id = {$persona_id}
                    GROUP BY periodos.id
                    ORDER BY periodos.year DESC, periodos.meses_Periodo DESC
                    LIMIT 1
                ");

            break;
        }

        $periodo_id = $periodo_reciente[0]->id ?? 1;
        header("Location: /dashboard?periodo_id={$periodo_id}&page=1");
        exit;
    }

        $periodo_filtro = "&periodo_id={$periodo_id}";
        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        // Validación de página
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header("Location: /dashboard?periodo_id={$periodo_id}&page=1");
            exit;
        }
        
        $registros_por_pagina = 6;
        
        // Para un admin nos traemos todos los registros de periodos
        $periodos = Periodos::SQL( "SELECT id, meses_Periodo, year FROM periodos ORDER BY year DESC, meses_Periodo DESC" );

        // Construimos la consulta base
        $query_base = "SELECT DISTINCT cursos.id, cursos.url AS curso_url,
            CONCAT(docentes.nombre_Docente, ' ',docentes.apellido_Paterno, ' ', docentes.apellido_Materno) AS nombre_docente,
            niveles.nombre_Nivel as nombre_Nivel,
            CONCAT(periodos.meses_Periodo, ' ', periodos.year) AS periodo,
             periodos.meses_Periodo, periodos.year,
            aulas.nombre_Aula as nombre_Aula,
            periodos.id as periodo_id
            FROM cursos 
            LEFT OUTER JOIN docentes ON cursos.docente_id = docentes.id 
            LEFT OUTER JOIN niveles ON cursos.nivel_id = niveles.id 
            LEFT OUTER JOIN periodos ON cursos.periodo_id = periodos.id 
            LEFT OUTER JOIN aulas ON cursos.aula_id = aulas.id
            LEFT OUTER JOIN alumno_curso_detalles ON cursos.id = alumno_curso_detalles.curso_detalle_id";

        // Añadimos condiciones según rol para la consulta de cursos y periodos
        $where = '';
        switch ($rol) {
            case 0: 
                //No hace falta agregar condiciones a la consulta base ni en los periodos para el admin
            break; // Admin
            case 1:
                $where = " WHERE docente_id = {$persona_id} ";
                //Buscamos solo los periodos donde el docente tiene cursos
                $periodos = Periodos::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos
                JOIN periodos ON cursos.periodo_id = periodos.id
                WHERE cursos.docente_id = {$persona_id}
                ORDER BY periodos.year DESC, periodos.meses_Periodo DESC");
            break; // Docente
            case 2: 
                $where = " WHERE alumno_curso_detalles.alumno_id = {$persona_id} ";
                //Buscamos solo los periodos donde el alumno tiene cursos
                $periodos = Periodos::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos
                JOIN periodos ON cursos.periodo_id = periodos.id
                JOIN alumno_curso_detalles ON cursos.id = alumno_curso_detalles.curso_detalle_id
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
        $cursos = CursosDetalles::SQL($query);

        // Obtenemos periodos disponibles
        //debuguear($cursos);
        $router->render('dashboard/index', [
            'titulo_pagina' => 'Cursos',
            'sidebar_nav' => 'Cursos',
            'alertas'=>$alertas,        
            'cursos' => $cursos,
            'paginacion' => $paginacion->paginacion(),
            'periodos' => $periodos,
            'periodo_seleccionado' => $periodo_id
        ]);
    }

    public static function crear_curso(Router $router)
    {
        $curso = new Cursos;
        isAuth();
        $alertas = [];
        $docentes = Docentes::all();
        $aulas = Aulas::all();
        $niveles = Niveles::all();
        $periodos = Periodos::all();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $curso = new Cursos($_POST['curso']);
            $curso->sincronizar($_POST);
            //Validación
            $alertas = $curso->validarCurso();
            if (empty($alertas)) 
            {
                // Generar una URL única
                $hash = md5(uniqid());
                $curso->url = $hash;
                // Guardar el proyecto
                $curso->guardar();
                $id_registro = $curso->id;
                if ($curso)
                {
                    $tabla = 'cursos';
                    $evento = new BitacoraEventos;
                    $evento->eventos(1, $id_registro, $tabla);
                    header('Location: /curso?id=' . $curso->url);
                    exit; // OBLIGATORIO para que se haga la redirección correctamente
                } 
                // Redireccionar
            }
        }

        $router->render('dashboard/crear-curso', [
        'titulo_pagina' => 'Cursos',
        'sidebar_nav' => 'Cursos',  
            'alertas' => $alertas,
            'docentes'=> $docentes,
            'aulas' => $aulas,
            'niveles' => $niveles,
            'periodos' => $periodos,
            'curso' => $curso
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
        $curso = Cursos::where('url', $pagina_actual);
            if(!$curso){
            header('Location: /dashboard');
            exit;
        }
        if ($_SESSION['rol'] === 1 && $curso->docente_id !== $_SESSION['persona_id']) 
        {
            header('Location: /dashboard');
            exit;
        }
        if ($_SESSION['rol'] === 2) 
        {
            $id_curso = $curso->id;
            $query = "SELECT * FROM alumno_curso_detalles AS acd 
            WHERE acd.curso_detalle_id = {$id_curso} 
            AND acd.alumno_id={$_SESSION['persona_id']}";
            $curso = AlumnoCursoDetalles::unicoSQL($query);
            if (!$curso->num_rows)
            {
                header('Location: /dashboard');
                exit;
            }

        }            
        $query = "SELECT cursos.id, cursos.url AS curso_url,
        CONCAT(docentes.nombre_Docente, ' ',docentes.apellido_Paterno, ' ', docentes.apellido_Materno) AS nombre_docente,
        niveles.nombre_Nivel as nombre_Nivel,
        CONCAT (periodos.meses_Periodo, ' ', periodos.year) AS periodo,
        aulas.nombre_Aula as nombre_Aula ";
        $query .= " FROM cursos LEFT OUTER JOIN docentes ";
        $query .= " ON cursos.docente_id = docentes.id ";
        $query .= " LEFT OUTER JOIN niveles ";
        $query .= " ON cursos.nivel_id = niveles.id ";
        $query .= " LEFT OUTER JOIN periodos ";
        $query .= " ON cursos.periodo_id = periodos.id ";
        $query .= " LEFT OUTER JOIN aulas ";
        $query .= " ON cursos.aula_id = aulas.id ";            
        $query .= " WHERE cursos.url = '{$pagina_actual}'";            
        $query .= " ORDER BY periodos.year DESC ";
        
        $curso = CursosDetalles::obtenerUnico($query);
        $router->render('dashboard/curso', [
        'titulo_pagina' => 'Cursos',
        'sidebar_nav' => 'Cursos',            
            'curso' => $curso,
            'titulo' => 'Curso'
        ]);
    }

    public static function eliminar_Curso()
    {
        $id = validarORedireccionar('/dashboard');
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
                    $curso = Cursos::find($id);
                    $resultado =  $curso->eliminar();
                    if (!campoVacio($resultado))
                    {
                        $tabla = 'cursos';
                        $id_curso = $curso->id;
                        if (!campoVacio($id_curso))
                        {
                            $_SESSION['mensaje_exito'] = 'El curso fue eliminado correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(3, $id_curso, $tabla);
                            header("Location: /dashboard");
                            exit;
                        }                          
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el curso, probablemente tenga alumno(s) asignado(s).';
                        header("Location: /dashboard");
                        exit;
                        }
                }
            }
        }
    }
    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/dashboard');
        $alertas = [];
        isAuth();
        $curso = Cursos::find($id);
        $docentes = Docentes::all();
        $aulas = Aulas::all();
        $niveles = Niveles::all();
        $periodos = Periodos::all();
        if ($curso == false)
        {
            header("Location: /dashboard");
        }
        $alertas = Cursos::getAlertas();
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
                        header('Location: /dashboard');
                        //header('Location: /curso-buscar?columna=docentes.id&dato=' . $curso->id);
                        exit;
                    } else  
                        {
                            $_SESSION['mensaje_exito'] = 'El curso fue actualizado correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(2, $id_curso, $tabla);
                            header('Location: /dashboard');
                            //header('Location: /curso-buscar?columna=docentes.id&dato=' . $curso->id);
                            exit;
                        }
            }

        }
        $alertas = Docentes::getAlertas();
        $router->render('/dashboard/actualizar',[
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

    public static function perfil(Router $router)
    {
        isAuth();
        $alertas= [];
        $usuario = Usuario::find($_SESSION['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();
            if (empty($alertas))
            {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario && $existeUsuario->id !== $usuario->id)
                {
                    // Mensaje de la existencia de otro usuario con el mismo email
                    // Por lo tanto no es posible guardar el registro con un email duplicado
                    Usuario::setAlerta('error', 'Email no válido, el email ya pertenece a otro usuario');
                    $alertas = $usuario->getAlertas();
                } else
                    {
                    // guardar el usuario
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                    //Asignar el nombre nuevo ala barra
                    $_SESSION['nombre'] = $usuario->nombre;
                    }   
            }
        }
        $router->render('dashboard/perfil', [
            'titulo_pagina' => 'Perfil',
            'sidebar_nav' => 'Perfil',     
            'usuario' => $usuario,
            'alertas' => $alertas,
        ]);
    }

    public static function cambiar_password(Router $router)
    {
        isAuth();
        $alertas= [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $usuario =  Usuario::find($_SESSION['id']);
            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if (empty($alertas))
            {
                $resultado = $usuario->comprobar_password();
                if ($resultado) 
                {
                    $usuario->password = $usuario->password_nuevo;

                    //  Eliminar propiedades no necesarios
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado)
                    {
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else
                    {
                        Usuario::setAlerta('error', 'Password Actual Incorrecto');
                        $alertas = $usuario->getAlertas();
                    }
            }
        }
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'titulo_pagina' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    } 
}
?>