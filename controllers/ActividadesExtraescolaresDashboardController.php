<?php

namespace Controllers;

use Model\Alumno;
use Model\Aula;
use MVC\Router;
use Model\Curso;
use Model\Periodo;
use Model\Usuario;
use Model\Personal;
use Model\TipoCurso;
use Classes\Paginacion;
use Model\CursoDetalles;
use Model\BitacoraEventos;
use Model\Curso_Requisitos;
use Model\AlumnoCursoDetalles;
use Model\ConfiguracionModuloPorPeriodo;

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
                case 3: // Coordinador de Actividades Extraescolares
                    $periodo_reciente = Periodo::SQL("SELECT id FROM periodos ORDER BY year DESC, meses_Periodo DESC LIMIT 1");
                break;
                case 4: // Instructor de Actividades Extraescolares
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
        $query_base  = "SELECT DISTINCT cursos.id, cursos.inscripcion_alumno, cursos.url AS curso_url, ";
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
                WHERE cursos.encargado_id = {$persona_id}
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

        // Agregamos los enalces para crear el curso 
        $crear = '/crear-curso-actividades-extraescolares';

        $router->render('actividades_extraescolares_dashboard/index', [
            'titulo_pagina' => 'Cursos de Actividades Extraescolares',
            'sidebar_nav' => 'Cursos de Actividades Extraescolares',
            'alertas'=> $alertas,        
            'cursos_actividades_extraescolares' => $cursos_actividades_extraescolares,
            'paginacion' => $paginacion->paginacion(),
            'periodos' => $periodos,
            'periodo_seleccionado' => $periodo_id,
            'crear' => $crear
        ]);
    }

    public static function crear_curso(Router $router)
    {
        // agregamos el enlace para importar cursos
        $crear_varios = '/importar-curso-actividades-extraescolares';

        $curso = new Curso;
        $curso_requisitos = new Curso_Requisitos;
        isAuth();
        $alertas = [];
        // Ahora necesitamos traernos al personal que tenga el rol de instructor de Actividades extraescolares segun este modulo
        // Construimos la consulta base
        $query_base  = "SELECT personal.id, personal.nombre, personal.apellido_Paterno, personal.apellido_Materno, personal.genero FROM personal ";
        $query_base .= "LEFT OUTER JOIN asignacion_roles ON asignacion_roles.id_personal = personal.id ";
        // El id del rol que pertenece a Instructor de actividades extraescolares es:
        // 4
        $query_base .= "WHERE asignacion_roles.id_rol = 4";
        $personal = Personal::SQL($query_base);
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
            // Creamos los objetos
            $curso = new Curso($_POST['curso']);            
            // Sincronizamos objeto de curso
            $curso->sincronizar($_POST);

            // Validamos si se van a poner requisitos
            if ($curso->requisitos === 'Si')
            {
                // En caso de que la variable sea 'Si' sincronizamos datos
                $curso_requisitos = new Curso_Requisitos($_POST['curso_requisitos']);
                $curso_requisitos->sincronizar($_POST);
                // Verificamos que sea numerico
                if (!is_numeric($curso_requisitos->minimo_aprobados))
                {
                    $alertas['error'][] = 'Coloque un minimo de actividades aprobadas necesarias para ingresar al curso';
                }
                // Verificamos que el valor sea como minimo mayor a 0
                else 
                {
                    if ($curso_requisitos->minimo_aprobados <= 0)
                    {
                        $alertas['error'][] = 'Cantidad de actividades aprobadas para ingresar al curso invalida';
                    }
                }
                // Si no hay alertar declaramos la variable de $requisitos que posteriormente indicara que deberan registrarse los requisitos
                if (empty($alertas))
                {
                    $requisitos = true;
                }
                else
                {
                    $requisitos = false;
                }
            }
            //Validación
            // Ahora vamos a validar las alertas previas para la cantidad de cursos necesarios para ingresar al curso actual
            // Y vamos a mezclarlas con las alertas para validar el curso
            $alertas = array_merge($alertas, $curso->validarCurso());
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
                    if ($requisitos === true) 
                    {
                        $curso_requisitos->id_curso = $id_registro;
                        $curso_requisitos->curso_excluido = $id_registro;
                        $resultado = $curso_requisitos->guardar();
                        if (!campoVacio($resultado))
                        {
                            //echo 'Se guardo un curso en con junto con sus requisitos';
                            $tabla = 'cursos y su requisito';
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
        
        $router->render('actividades_extraescolares_dashboard/crear-curso-actividad-extraescolar', [
        'titulo_pagina' => 'Crear Nuevo Curso de Actividades Extraescolares',
        'sidebar_nav' => 'Cursos de Actividades Extraescolares',  
            'alertas' => $alertas,
            'personal'=> $personal,
            'aulas' => $aulas,
            'tipos_curso' => $tipos_curso,
            'periodos' => $periodos,
            'curso' => $curso,
            'curso_requisitos' => $curso_requisitos,
            'crear_varios' => $crear_varios
        ]);
    }

    public static function importar_curso(Router $router)
    {
        isAuth();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $archivotmp = $_FILES['dataCursos']['tmp_name'];
            $lineas     = file($archivotmp);

            $i = 0;

            foreach ($lineas as $linea)
            {
                if ($i == 0) { // Saltar encabezado
                    $i++;
                    continue;
                }

                $datos = str_getcsv(trim($linea), ",");
                // Convertimos a array como si fuera $_POST
                $datosAlumno = [
                    'id'                   => $datos[0] ?? '',
                    'nombre_Alumno'        => $datos[1] ?? '',
                    'apellido_Paterno'     => $datos[2] ?? '',
                    'apellido_Materno'     => $datos[3] ?? '',
                    'comentarios'          => $datos[4] ?? '',
                    'id_Carrera'           => $datos[5] ?? '',
                    'telefono'             => $datos[6] ?? '',
                    'correo_institucional' => $datos[7] ?? ''
                ];
                // Crear objeto y sincronizar
                $alumno = new Alumno($datosAlumno);
                $alumno->sincronizar($datosAlumno);

                // Verificar si existe en DB
                $existe = Alumno::find($alumno->id); // Método que busque por ID
                if ($existe)
                {
                    $alumno->actualizar();
                } else  {
                            $alumno->guardarAlumno();
                        }

                        $i++;                

                }
                $_SESSION['mensaje_exito'] = 'Alumnos importados correctamente.';                    
                header('Location: /alumnos');
                exit;
        }
        

        // Agregamos los enalces para crear el curso 
        $crear = '/crear-curso-actividades-extraescolares';

        $router->render('actividades_extraescolares_dashboard/importar-curso-actividades-extraescolares', [
        'titulo_pagina' => 'Importar Cursos de Actividades Extraescolares',
        'sidebar_nav' => 'Cursos de Actividades Extraescolares',  
            'alertas' => $alertas,
            'crear' => $crear
        ]);
    }

    public static function curso(Router $router)
    {
        isAuth();
        $pagina_actual = $_GET['id'] ?? 1;
        $pagina_actual = s($pagina_actual);
        $persona_id = $_SESSION['persona_id'];
        $alertas= [];
        if (!$pagina_actual)
        {
            header('Location: /dashboard');
        }

        // Validamos que existe un curso con el id proporcionado
        $curso = Curso::where('url', $pagina_actual);
        if (campoVacio($curso))
        {
            header("Location: /dashboard");
            exit;
        }
        // Verificamos si el curso tiene requisitos
        if ($curso->requisitos === 'Si')
        {
            $curso_requisitos = Curso_Requisitos::where('id_curso', $curso->id);
            //Se valida que existan los requisitos en la tabla de cursos_requisitos
            if (!campoVacio($curso_requisitos))
            {
                $existen_requisitos = true;
            }
        }
        else
        {
            $curso_requisitos = null;
            $existen_requisitos = false;
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
            'curso_requisitos' => $curso_requisitos,
            'titulo' => 'Actividad Extraescolar',
            'alertas' => $alertas

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
                    // Una vez capturamos el id verificamos si a este curso se le asignaron requisitos en las sig tablas
                    // curso_requisitos
                    // alumno_curso_detalles
                    // horarios_clase
                    // si tiene alguno no lo borramos hasta que vaya borrando cada uno de esos registros
                    // guardamos el id del curso encontrado
                    // $id_curso = $curso->id;
                    // $curso_Requisitos = Curso_Requisitos::where('id_curso',$id_curso);
                    // if (!campoVacio($curso_Requisitos))
                    // {
                    //     // Si se encuentra un registro para cursos requisitos, lo eliminamos
                    //     $curso_Requisitos->eliminar();
                    // }
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
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el curso, probablemente tenga otros registros asociados, verifique con el administrador del sistema).';
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
        $personal = Personal::all();
        $aulas = Aula::all();
        $tipos_curso = TipoCurso::all();
        $periodos = Periodo::all();
        // Validamos que existe un curso con el id proporcionado
        $curso = Curso::find($id);
        if ($curso == false)
        {
            header("Location: /dashboard");
        }
        // Verificamos si el curso tiene requisitos
        if($curso->requisitos === 'Si')
        {
            $curso_requisitos = Curso_Requisitos::where('id_curso', $id);
        }
        // Si no existe el registro lo ponemos como null
        else if($curso->requisitos === 'No')
        {
            $curso_requisitos = null;
        }
        $alertas = Curso::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Sincronizamos valores
            // para curso
            $args = $_POST['curso'];
            $curso->sincronizar($args);
            // ahora validamos alertas de curso primero
            // para curso
            $alertas = $curso->validarCurso();
            // Si no hay alertas posteriormente validamos requisitos
            if(empty($alertas))
            {
                // Validamos si se van a poner requisitos
                if ($curso->requisitos === 'Si')
                {
                    // Creamos el curso de requisitos
                    $curso_requisitos = new Curso_Requisitos($_POST['curso_requisitos']);
                    $curso_requisitos->sincronizar($_POST);

                    // Validamos si es numerico
                    if (!is_numeric($curso_requisitos->minimo_aprobados))
                    {
                        $alertas['error'][] = 'Coloque un minimo de actividades aprobadas necesarias para ingresar al curso';
                    }
                    // Verificamos que el valor sea como minimo mayor a 0
                    else if ($curso_requisitos->minimo_aprobados <= 0)
                    {
                        $alertas['error'][] = 'Cantidad de actividades aprobadas para ingresar al curso invalida';
                    }
                    // Si no hay alertar declaramos la variable de $requisitos que posteriormente indicara que deberan registrarse los requisitos
                    //debuguear($alertas);
                    if (empty($alertas))
                    {
                        // Capturamos el id del curso
                        $id_registro_curso = $curso->id;
                        $requisitos = true;
                        // Ahora buscamos si hay un registro que de requisitos para el curso
                        $curso_requisitos_existente = Curso_Requisitos::where('id_curso', $id_registro_curso);
                        // Si ya hay un registro lo traemos y actualizamos el valor de la propiedad
                        if (!campoVacio($curso_requisitos_existente))
                        {
                            //Sincronizamos valores de curso_requisitos en la propiedad de minimo_aprobados
                            // y guardamos el registro
                            $curso_requisitos_existente->minimo_aprobados = $curso_requisitos->minimo_aprobados;
                            $resultado = $curso_requisitos_existente->guardar();
                            $requisitos_guardados = (!campoVacio($resultado)) ? true : false;                            
                        }
                        else
                        {
                            // Si no exite creamos el registro
                            $curso_requisitos->id_curso = $id_registro_curso;
                            $curso_requisitos->curso_excluido = $id_registro_curso;
                            $resultado = $curso_requisitos->guardar();
                            $requisitos_guardados = (!campoVacio($resultado)) ? true : false;                            

                        }
                        // Si los requisitos se guardaron
                        if ($requisitos_guardados === true)
                        {
                            // Actualizamos curso unicamente
                            $curso->actualizar();
                            $tabla = 'cursos';
                            $id_curso = $curso->id;
                            if (campoVacio($id_curso))
                            {
                                $_SESSION['mensaje_error'] = 'El curso no se actualizo.';
                                header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                                exit; // OBLIGATORIO para que se haga la redirección correctamente    
                            } 
                            else  
                            {
                                $_SESSION['mensaje_exito'] = 'El curso fue actualizado correctamente.';
                                $evento = new BitacoraEventos;
                                $evento->eventos(2, $id_curso, $tabla);
                                header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                                exit; // OBLIGATORIO para que se haga la redirección correctamente
                            }                        
                        }
                        
                    }
                }
                if ($curso->requisitos === 'No' ) 
                {
                    // Buscamos el objeto en la tabla de cursos requisitos por si existe y eliminamos el dato
                    $curso_requisitos = Curso_Requisitos::where('id_curso', $id);
                    if (!campoVacio($curso_requisitos))
                    {
                        $curso_requisitos->eliminar();
                    }
                    // Actualizamos curso unicamente
                    $curso->actualizar();
                    $tabla = 'cursos';
                    $id_curso = $curso->id;
                    if (campoVacio($id_curso))
                    {
                        $_SESSION['mensaje_error'] = 'El curso no se actualizo.';
                        header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                        exit; // OBLIGATORIO para que se haga la redirección correctamente    
                    } 
                    else  
                    {
                        $_SESSION['mensaje_exito'] = 'El curso fue actualizado correctamente.';
                        $evento = new BitacoraEventos;
                        $evento->eventos(2, $id_curso, $tabla);
                        header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                        exit; // OBLIGATORIO para que se haga la redirección correctamente
                    }
                }
            }

        }
         $router->render('actividades_Extraescolares_dashboard/curso-actualizar-actividad-extraescolar',[
            'curso'=> $curso,
            'curso_requisitos' => $curso_requisitos,
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
                if(validarTipoContenido($tipo)) 
                {
                    $curso = Curso::find($id);
                    if(!campoVacio($curso))
                    {
                        if(!campoVacio($_POST['curso']['estado'])) 
                        {
                            $nuevo_estado = $_POST['curso']['estado'];
                            $curso->estado = $nuevo_estado;
                            $alertas = $curso->validar();
                            if(empty($alertas))
                            {
                                $resultado = $curso->guardar();
                                $tabla = 'curso';
                                $resultado = $resultado['resultado'];
                                if(!campoVacio($resultado))
                                {
                                    $_SESSION['mensaje_exito'] = 'El curso fue actualizado correctamente.';
                                    $evento = new BitacoraEventos;
                                    $evento->eventos(2, $id_curso, $tabla);
                                    header('Location: /curso-actividades-extraescolares?id=' . $curso->url);
                                    exit; // OBLIGATORIO para que se haga la redirección correctamente
                                }    
                            }
                        }
                    } 
                    else 
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

    public static function configuracion_modulo(Router $router)
    {
        $alertas = [];
        // Primero vamos a traernos los periodos activos
        // Seran capturados en la url como periodo_id
        $periodo_id = $_GET['periodo_id'] ?? null;
        // Si no hay periodo aplicamos un if para validarlo y aplicarlo
        if ($periodo_id === false || $periodo_id === null)
        {
            $periodo_reciente = Periodo::SQL("SELECT periodos.id, periodos.meses_Periodo, periodos.year, periodos.estado, periodos.fecha_inicio, periodos.fecha_fin FROM periodos ");
            $periodo_id = $periodo_reciente[0]->id ?? 1;
            header("Location: /configuracion-modulo-actividades-extraescolares?periodo_id={$periodo_id}&page=1");
            exit;
        }
        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        // Validamos que la pagina no sea menor a 1 o no exista
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header("Location: /configuracion-modulo-actividades-extraescolares?periodo_id={$periodo_id}&page=1");
            exit;
        }
        // Construimos la consulta base
        $query_base  = "SELECT periodos.id, periodos.meses_Periodo, periodos.year, periodos.estado, periodos.fecha_inicio, periodos.fecha_fin ";
        $query_base .= "FROM periodos ";
        // donde el estado del periodo sea activo
        $query_base .= "WHERE periodos.estado = 'Activo'";
        // Aplicamos filtro de periodo (obligatorio)
        $query_base .= " AND periodos.id = {$periodo_id} ";
        $periodo = Periodo::SQL($query_base);
        // Ahora necesitamos traernos el registro de configuracion de modulo en el periodo activo
        $configuracion_modulo_por_periodo = ConfiguracionModuloPorPeriodo::where('id_periodo', $periodo_id);
        if (campoVacio($configuracion_modulo_por_periodo)) 
        {
            // Si no hay registro se crea un nuevo objeto 
            $configuracion_modulo_por_periodo = new ConfiguracionModuloPorPeriodo();
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // Creamos el objeto
            $configuracion_modulo_por_periodo = new ConfiguracionModuloPorPeriodo($_POST['configuracion_modulo_periodo']);
            // planeaba enviar el id del modulo desde el form pero es mas seguro asignarlo aqui

            // Sincronizamos los valores posteados
            $configuracion_modulo_por_periodo->sincronizar($_POST);
            $configuracion_modulo_por_periodo->id_modulo = 6;
            
            // Ahora hay que validar si puso maximo de cursos por periodo o fecha limite de inscripcion para llamar alas respectivas funciones 
            // dichas funciones estan en el objeto
            debuguear($configuracion_modulo_por_periodo);

        }
        $router->render('actividades_extraescolares_dashboard/configuracion-modulo-actividades-extraescolares', [
            'titulo_pagina' => 'Configuracion del modulo de actividades extraescolares',
            'sidebar_nav' => 'Configuracion Modulo',
            'alertas' => $alertas,
            // Ya que estamos utilizando un template que se usa en el index de este modulo dejaremos
            // que la variable de $periodo pase ala vista como periodos y evitar conflictos
            'periodos' => $periodo,
            'periodo_seleccionado' => $periodo_id,
            'configuracion_modulo_por_periodo' => $configuracion_modulo_por_periodo

        ]);
    }
}
?>