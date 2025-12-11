<?php

namespace Controllers;

use MVC\Router;
use Model\Aula;
use Model\Usuario;
use Model\Periodo;
use Classes\Paginacion;
use Model\AlumnoCursoDetalles;
use Model\BitacoraEventos;
use Model\Curso;
use Model\Curso_Requisitos;
use Model\CursoDetalles;
use Model\Personal;
use Model\TipoCurso;

class ActividadesExtraescolaresDashboardController 
{
    public static function index(Router $router)
    {
        isAuth();
        $rol = $_SESSION['rol'];
        $alertas = [];
        $persona_id = $_SESSION['persona_id'];
        //debuguear($_SESSION);
        // Obtenemos parámetros de filtrado
        $periodo_id = $_GET['periodo_id'] ?? null;
        
        // Redirigir inmediatamente si no hay periodo seleccionado
        if ($periodo_id === false || $periodo_id === null)
        {
            switch ($rol) {
                case 3: // Admin
                    $periodo_reciente = Periodo::SQL("SELECT id FROM periodos ORDER BY year DESC, meses_Periodo DESC LIMIT 1");
                break;
                case 4: // Docente
                    $periodo_reciente = Periodo::SQL("
                        SELECT periodos.id
                        FROM periodos
                        JOIN cursos ON cursos.periodo_id = periodos.id
                        WHERE cursos.encargado_id = {$persona_id}
                        GROUP BY periodos.id
                        ORDER BY periodos.year DESC, periodos.meses_Periodo DESC
                        LIMIT 1
                    ");
                break;
                case 1; // Alumno
                    $periodo_reciente = Periodo::SQL("
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
            header("Location: /actividades-extraescolares?periodo_id={$periodo_id}&page=1");
            exit;
        }

        $periodo_filtro = "&periodo_id={$periodo_id}";
        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        // Validación de página
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header("Location: /actividades-extraescolares?periodo_id={$periodo_id}&page=1");
            exit;
        }
        
        $registros_por_pagina = 6;
        
        // Para un admin nos traemos todos los registros de periodos
        $periodos = Periodo::SQL( "SELECT id, meses_Periodo, year FROM periodos ORDER BY year DESC, meses_Periodo DESC" );

        // Construimos la consulta base
        $query_base  = "SELECT DISTINCT cursos.id, cursos.url AS curso_url, ";
        $query_base .= "CONCAT(personal.nombre, ' ', personal.apellido_Paterno, ' ', personal.apellido_Materno) AS nombre_encargado, ";
        $query_base .= "tipos_curso.nombre_curso AS nombre_curso, ";
        $query_base .= "CONCAT(periodos.meses_Periodo, ' ', periodos.year) AS periodo, ";
        $query_base .= "periodos.meses_Periodo, periodos.year, "; 
        $query_base .= "aulas.nombre_Aula AS nombre_Aula ";
        $query_base .= "FROM cursos ";
        $query_base .= "LEFT OUTER JOIN personal ON cursos.encargado_id = personal.id ";
        $query_base .= "LEFT OUTER JOIN tipos_curso ON cursos.tipo_curso_id = tipos_curso.id ";
        $query_base .= "LEFT OUTER JOIN modulos ON tipos_curso.modulo_id = modulos.id ";
        $query_base .= "LEFT OUTER JOIN periodos ON cursos.periodo_id = periodos.id ";
        $query_base .= "LEFT OUTER JOIN aulas ON cursos.aula_id = aulas.id ";
        $query_base .= "LEFT OUTER JOIN alumno_curso_detalles ON cursos.id = alumno_curso_detalles.curso_detalle_id ";
        $query_base .= "WHERE modulos.id = 6";

        // Tabla de modulos y sus registros
        // 1 Creditos Complementarios
        // 2 Residencia Profesional
        // 3 Coordinacion de Lenguas Extranjeras
        // 4 Titulaciones
        // 5 Caja
        // 6 Actividades Extraescolares
        // Modulo actual numero 6 Actividades Extraescolares
        $where = "WHERE modulos.id = 6";

        // Añadimos condiciones según rol para la consulta de cursos y periodos
        $where = '';
        switch ($rol) {
            case 3: // Coordinador de actividades extraescolares
                //No hace falta agregar condiciones a la consulta base ni en los periodos para el admin
            break; 
            case 4:// Instructor de actividades extraescolares
                $where .= " AND encargado_id = {$persona_id} ";
                //Buscamos solo los periodos donde el docente tiene cursos
                $periodos = Periodo::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos
                JOIN periodos ON cursos.periodo_id = periodos.id
                WHERE cursos.instructor_id = {$persona_id}
                ORDER BY periodos.year DESC, periodos.meses_Periodo DESC");
            break; // Alumno
            case 1: 
                $where.= " AND WHERE alumno_curso_detalles.alumno_id = {$persona_id} ";
                //Buscamos solo los periodos donde el alumno tiene cursos
                $periodos = Periodo::SQL("SELECT DISTINCT periodos.id, periodos.meses_Periodo, periodos.year
                FROM cursos
                JOIN periodos ON cursos.periodo_id = periodos.id
                JOIN alumno_curso_detalles ON cursos.id = alumno_curso_detalles.curso_detalle_id
                WHERE alumno_curso_detalles.alumno_id = {$persona_id}
                ORDER BY periodos.year DESC, periodos.meses_Periodo DESC");                
            break; // Alumno
        }
        // Aplicamos filtro de periodo (obligatorio)
        $where .= " AND periodos.id = {$periodo_id} ";

        // Consulta para contar registros
        $query_conteo = $query_base . $where;
        $total_registros = CursoDetalles::SQLContar($query_conteo);
        // Paginación
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total_registros, $periodo_filtro);

        // Consulta paginada
        $query = $query_base . $where . " ORDER BY periodos.year DESC, periodos.meses_Periodo DESC ";
        $query .= " LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";
        // Obtenemos el total de los cursos por periodos
        $cursos_actividades_extraescolares = CursoDetalles::SQL($query);

        $router->render('actividades_extraescolares_dashboard/index', [
            'titulo_pagina' => 'Cursos de Actividades Extraescolares',
            'sidebar_nav' => 'Cursos de Actividades Extraescolares',
            'alertas'=>$alertas,        
            'cursos_actividades_extraescolares' => $cursos_actividades_extraescolares,
            'paginacion' => $paginacion->paginacion(),
            'periodos' => $periodos,
            'periodo_seleccionado' => $periodo_id
        ]);
    }

    public static function crear_curso(Router $router)
    {
        $curso = new Curso;
        $curso_Requisitos = new Curso_Requisitos;
        isAuth();
        $alertas = [];
        $encargados = Personal::all();
        $aulas = Aula::all();
        // Debido a que cada modulo gestionara sus propios tipos de cursos vamos a realizar una consulta filtrando los registros
        // de la tabla de tipos de cursos por modulo_id en base ala siguiente informacion
        // Tabla de modulos y sus registros
        // 1 Creditos Complementarios
        // 2 Residencia Profesional
        // 3 Coordinacion de Lenguas Extranjeras
        // 4 Titulaciones
        // 5 Caja
        // 6 Actividades Extraescolares
        // Modulo actual numero 6 Actividades Extraescolares

        //$tipos_curso = TipoCurso::whereAll('modulo_id', 6);
        // cON LA FUNCION whereAll nos traemos todos los registros filtrandolos por la columna indicada y su valor
        $tipos_curso = TipoCurso::belongsTo('modulo_id', 6);
        $periodos = Periodo::all();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $curso = new Curso($_POST['curso']);
            $curso_Requisitos = new Curso_Requisitos($_POST['curso_requisitos']);
            if($_POST['curso']['limite_alumnos'] !== 'Null' || $_POST['curso']['limite_alumnos'] <= '0'  || campoVacio($_POST['curso']['limite_alumnos'])) 
            {
                $alertas['error'][] = 'Introduzca una cantidad valida para el limite de cupos';
            } 
            if($_POST['curso_requisitos']['minimo_aprobados'] !== 'Null' && $_POST['curso_requisitos']['minimo_aprobados'] <= '0'  || campoVacio($_POST['curso_requisitos']['minimo_aprobados'])) 
            {
                $alertas['error'][] = 'Introduzca una cantidad valida para el limite de de cursos requeridos para ingresar al curso actual';
            }
            else 
            {
                $curso->sincronizar($_POST);
                $curso_Requisitos->sincronizar($_POST);
                // Verificamos si se coloro un minimo de cursos aprobados
                if ($curso_Requisitos->minimo_aprobados !== 'Null' && is_numeric($curso_Requisitos->minimo_aprobados) && $curso_Requisitos->minimo_aprobados >= 1)
                {
                    $requisitos = true;
                } 
                else
                {
                    $requisitos = false;
                }
               //Validación
               // Primero validamos curso
                $alertas = $curso->validarCurso();
                if (empty($alertas)) 
                {                 
                    // Generar una URL única
                    $hash = md5(uniqid());
                    $curso->url = $hash;
                    // Guardar el curso
                    //debuguear($curso);
                    $resultado = $curso->guardar();

                    $id_registro = $resultado['id'];
                    if (!campoVacio($id_registro))
                    {
                        // Si el curso se guardo entonces verificamos si el usuario coloco requisitos
                        if (isset($requisitos) && $requisitos === true) 
                        {
                            $curso_Requisitos->id_curso = $id_registro;
                            $curso_Requisitos->curso_excluido = $id_registro;
                            $resultado = $curso_Requisitos->guardar();

                            if (!campoVacio($resultado))
                            {
                                //echo 'Se guardo un curso en con junto con sus requisitos';
                                $tabla = 'cursos';
                                $evento = new BitacoraEventos;
                                $evento->eventos(1, $id_registro, $tabla);
                                header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                                exit; // OBLIGATORIO para que se haga la redirección correctamente
                            }
                        } 
                        else
                        {
                                $tabla = 'cursos';
                                $evento = new BitacoraEventos;
                                $evento->eventos(1, $id_registro, $tabla);
                                header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                                exit; // OBLIGATORIO para que se haga la redirección correctamente                               
                        }
                    }   
                }
            }
        }
        
        $router->render('actividades_extraescolares_dashboard/crear-curso-actividad-extraescolar', [
        'titulo_pagina' => 'Crear Nuevo Curso de Actividades Extraescolares',
        'sidebar_nav' => 'Cursos de Actividades Extraescolares',  
            'alertas' => $alertas,
            'encargados'=> $encargados,
            'aulas' => $aulas,
            'tipos_curso' => $tipos_curso,
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
        $curso = Curso::where('url', $pagina_actual);
            if(!$curso){
            header('Location: /dashboard');
            exit;
        }
        if ($_SESSION['rol'] === 1 && $curso->encargado_id !== $_SESSION['persona_id']) 
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
        $query = "SELECT cursos.inscripcion_alumno, cursos.limite_alumnos, cursos.estado, cursos.id, cursos.url AS curso_url, 
        CONCAT(personal.nombre, ' ',personal.apellido_Paterno, ' ', personal.apellido_Materno) AS nombre_encargado,
        tipos_curso.nombre_curso as nombre_curso,
        CONCAT (periodos.meses_Periodo, ' ', periodos.year) AS periodo,
        aulas.nombre_Aula as nombre_Aula ";
        $query .= " FROM cursos LEFT OUTER JOIN personal ";
        $query .= " ON cursos.encargado_id = personal.id ";
        $query .= " LEFT OUTER JOIN tipos_curso ";
        $query .= " ON cursos.tipo_curso_id = tipos_curso.id ";
        $query .= " LEFT OUTER JOIN periodos ";
        $query .= " ON cursos.periodo_id = periodos.id ";
        $query .= " LEFT OUTER JOIN aulas ";
        $query .= " ON cursos.aula_id = aulas.id ";            
        $query .= " WHERE cursos.url = '{$pagina_actual}'";            
        $query .= " ORDER BY periodos.year DESC ";
        
        $actividad_extraescolar_curso = CursoDetalles::obtenerUnico($query);
        $router->render('actividades_Extraescolares_dashboard/curso-actividad-extraescolar', [
            'titulo_pagina' => 'Curso de Actividades Extraescolares',
            'sidebar_nav' => 'Cursos de Actividades Extraescolares',         
            'actividad_extraescolar_curso' => $actividad_extraescolar_curso,
            'titulo' => 'Actividad Extraescolar'
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
                    $curso = Curso::find($id);
                    $resultado =  $curso->eliminar();
                    if (!campoVacio($resultado))
                    {
                        $tabla = 'curso';
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
        $curso = Curso::find($id);
        $docentes = Personal::all();
        $aulas = Aula::all();
        $niveles = TipoCurso::all();
        $periodos = Periodo::all();
        if ($curso == false)
        {
            header("Location: /dashboard");
        }
        $alertas = Curso::getAlertas();
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
         $router->render('/dashboard/actualizar',[
            'curso'=>$curso,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Curso',
            'sidebar_nav' => 'Cursos',
            'personal'=> $personal,
            'aulas' => $aulas,
            'tipos_curso' => $tipos_curso,
            'periodos' => $periodos,
        ]);
    }


    public static function actualizar_estado_curso()
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
                    $curso = Curso::find($id);
                    if (!campoVacio($curso))
                    {
                        if (!campoVacio($POST['curso']['estado'])) 
                        {
                            $nuevo_estado = $POST['curso']['estado'];
                            $curso->estado = $nuevo_estado;
                            $alertas = $curso->validar();
                            if (empty($alertas)) {
                                $tabla = 'curso';
                                $id_curso = $curso->id;
                                if (!campoVacio($id_curso))
                                {
                                    $_SESSION['mensaje_exito'] = 'El curso fue eliminado correctamente.';
                                    $evento = new BitacoraEventos;
                                    $evento->eventos(3, $id_curso, $tabla);
                                    header("Location: /dashboard");
                                    exit;
                                }    
                            }
                        }
                                              
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible actualizar el estado del curso, consulte con el proveedor por posibles soluciones.';
                        header("Location: /dashboard");
                        exit;
                        }
                }
            }
        }
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