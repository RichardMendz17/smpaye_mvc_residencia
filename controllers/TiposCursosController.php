<?php

namespace Controllers;

use MVC\Router;
use Model\TipoCurso;
use Model\BitacoraEventos;
use Classes\Paginacion;
use Model\Modulo;
use Model\Tipo_Curso_Detalles;
use Model\TipoCursoDetalles;

class TiposCursosController {
    public static function index(Router $router)
    {
        $alertas = [];
        isAuth();

        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        //Validamos que la pagina actual exista y que no sea menor a 1
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header('Location: /tipos-curso?page=1');
        }

        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;


        $query = "SELECT tipos_curso.id, tipos_curso.nombre_curso, modulos.nombre_modulo ";
        $query .= "FROM tipos_curso ";
        $query .= "LEFT OUTER JOIN modulos ON tipos_curso.modulo_id = modulos.id ";

        // Identificamos el rol
        // dependiendo del rol le mostraremos los tipos de cursos correspondiente al modulo del rol
        $rol = $_SESSION['rol'];
        switch ($rol) {
            case 0:
                //0 es admin muestra todos
                // Traemos el total de registros
                $total = TipoCurso::total();
                break;
            case 3:
                //3 Coordinador de Actividades Extraescolares
                $modulo_id = '6';
                $query .= " WHERE modulos.id = $modulo_id";
                $query_total_tipos_cursos = "SELECT * FROM tipos_curso WHERE modulo_id = $modulo_id ";
                $sidevar_nav = 'Tipos de Actividades Extraescolares';
                $total = TipoCurso::contarConFiltros($query_total_tipos_cursos);
                break;
            case 5:
                //5 Coordinador de Creditos Complementarios
                $modulo_id = '1';
                $query .= " WHERE modulos.id = $modulo_id";
                $query_total_tipos_cursos = "SELECT * FROM tipos_curso WHERE modulo_id = $modulo_id ";
                $total = TipoCurso::contarConFiltros($query_total_tipos_cursos);

                break;
            case 10:
                //10 Coordinador de lenguas Extranjeras
                $modulo_id = '3';
                $query .= " WHERE modulos.id = $modulo_id";
                $query_total_tipos_cursos = "SELECT * FROM tipos_curso WHERE modulo_id = $modulo_id ";
                $total = TipoCurso::contarConFiltros($query_total_tipos_cursos);

                break;
            default:
                # code...
                break;
        }


        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        //$tipos_curso = TipoCurso::paginar($registros_por_pagina, $paginacion->offset());

        $query .= " ORDER BY tipos_curso.id DESC ";
        $query .= "LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";

        $tipos_curso_detalles = TipoCursoDetalles::SQL($query);
        $alertas = TipoCurso::getAlertas();        
        $router->render('tipos-curso/index', [
            'titulo_pagina' => 'Tipos de Cursos',
            'sidebar_nav' => $sidevar_nav,            
            'tipos_curso'=>$tipos_curso_detalles,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion()            
            
        ]);
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        isAuth();
        $modulos = Modulo::all();
        $tipo_curso = new TipoCurso;
        $alertas = TipoCurso::getAlertas();

        $rol = $_SESSION['rol'];
        switch ($rol) {
            case 0:
                //0 no hace falta colocarle un modulo_id
                $modulo_id = Null;
                break;
            case 3:
                //3 Coordinador de Actividades Extraescolares
                $modulo_id = '6';
                break;
            case 5:
                //5 Coordinador de Creditos Complementarios
                $modulo_id = '1';
                break;
            case 10:
                //10 Coordinador de lenguas Extranjeras
                $modulo_id = '3';
                break;
            default:
                # code...
                break;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $tipo_curso = new TipoCurso($_POST['tipo_curso']);
            $tipo_curso->sincronizar($_POST);
            $alertas = $tipo_curso->validar();
            if(empty($alertas))
            {
                // Ahora solo tenemos que verificar que el valor del modulo_id coincida con el 
                // del usuario en respecto a su rol
                $rol = $_SESSION['rol'];
                if ($rol !== 0)
                {
                    $verificacion_nombre_y_modulo = true;
                }
                else
                {
                    $verificacion_nombre_y_modulo = false;
                }
                switch ($rol) {
                case 0:
                    //0 no hace falta colocarle un modulo_id

                    break;
                case 3:
                    //3 Coordinador de Actividades Extraescolares
                    //debuguear($tipo_curso);
                    if ($tipo_curso->modulo_id != 6) {
                        $alertas['error'][] = 'Datos invalidos';
                    }
                    break;
                case 5:
                    //5 Coordinador de Creditos Complementarios
                    if ($tipo_curso->modulo_id != 5) {
                        $alertas['error'][] = 'Datos invalidos';
                    }
                    break;
                case 10:
                    //10 Coordinador de lenguas Extranjeras
                    if ($tipo_curso->modulo_id != 10) {
                        $alertas['error'][] = 'Datos invalidos';
                    }
                    break;
                default:
                    # code...
                    break;
                }
                if ($verificacion_nombre_y_modulo == true) 
                {
                    $verificacion_nombre_y_modulo = TipoCurso::buscarPorMultiples(
                        [
                            'nombre_curso',
                            'modulo_id'
                        ], 
                        [
                            $tipo_curso->nombre_curso,
                            $tipo_curso->modulo_id
                        ]);
                        if (!campoVacio($verificacion_nombre_y_modulo)) {
                            $alertas['error'][] = 'El Tipo de Curso ya existe en este modulo';
                        } 
                        else 
                        {
                            $resultado = $tipo_curso->guardar();
                            $tabla = 'tipos_curso';
                            $id_registro = $resultado['id'];
                            if ($id_registro)
                            {
                                $_SESSION['mensaje_exito'] = 'El tipo de curso "' . $tipo_curso->nombre_curso . '" fue creado correctamente.';                    
                                $evento = new BitacoraEventos;
                                $evento->eventos(1, $id_registro, $tabla);
                                header('Location: /tipos-curso');
                                exit;
                            }
                        }
                }
            }
        }
        $router->render('/tipos-curso/crear', [
            'tipo_curso' => $tipo_curso,
            'modulos' => $modulos,
            'alertas' => $alertas,
            'modulo_id'=> $modulo_id,
            'titulo_pagina' => 'Crear Tipo de Curso',
            'sidebar_nav' => 'Tipos Curso',              
            
        ]);
    }

    public static function actualizar(Router $router)
    {
        $alertas = [];
        isAuth();

        $id = validarORedireccionar('/niveles');
        $nivel = TipoCurso::find($id);
        $alertas = TipoCurso::getAlertas();      
        if ($nivel == false)
        {
            header("Location: /niveles");
        }
        // Ejecutar el código despues de que el usuario envia el formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Asignar los valores
            $args = $_POST['nivel'];
            //Sincronizar objeto en memoria con lo que el usuario escribió
            $nivel->sincronizar($args);
            // Validación
            $alertas = $nivel->validar();
            if (empty($alertas)) 
            {
                $verificar = TipoCurso::where('nombre_Nivel', $nivel->nombre_Nivel);
                if($verificar)
                {
                    $alertas['error'][] = 'El Nivel ya esta registrado';
                } else
                    {
                    $nivel->guardar();
                    $tabla = 'niveles';
                    $id_registro = $nivel->id;
                        if ($id_registro)
                        {
                            $_SESSION['mensaje_exito'] = 'El Nivel fue actualizado correctamente';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(1, $id_registro, $tabla);
                            header('Location: /niveles');
                            exit; // OBLIGATORIO para que se haga la redirección correctamente
                        }                    
                    }
            }
        }
        $router->render('/niveles/actualizar',[
            'nivel'=>$nivel,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Nivel',
            'sidebar_nav' => 'Niveles',            
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/niveles');
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //Validar id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if($id !== false && $id >= 0)
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo))
                {
                    $nivel = TipoCurso::find($id);
                    $resultado =  $nivel->eliminar();
                    if ($resultado !== null && $resultado !== false)
                    {
                        $tabla = 'niveles';
                        $id_registro = $nivel->id;
                        if (!campoVacio($id_registro))
                        {
                            $_SESSION['mensaje_exito'] = 'El nivel fue eliminado correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(3, $id_registro, $tabla);
                            header("Location: /niveles");
                            exit;
                        }
                    } else 
                        {
                            $_SESSION['mensaje_error'] = 'No fue posible eliminar el nivel, probablemente este siendo utilizada por un registro.';
                            header("Location: /niveles");
                            exit;
                        }                      
                }
            }
        }
    }    


    public static function buscar(Router $router)
    {
        $id = validarORedireccionar('/niveles-buscar');
        isAuth();
        $alertas = []; 
        $tipos_curso =  NULL;
        $paginacion = null;
        //Obtener las columnas para elegir por cual buscar
        $columnasDB = Tipo_Curso_Detalles::obtenerColumnas();
        $registro = $_POST['dato'] ?? $_GET['dato'] ?? '';
        $columna_Seleccionada = $_POST['columna'] ?? $_GET['columna'] ?? '';
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        { 
            if ($columna_Seleccionada !== '' && $registro !== '')
            {
            // Verificar que la columna sea válida
            if (!in_array($columna_Seleccionada, $columnasDB))
            {
                TipoCurso::setAlerta('error', 'Columna no válida');
            } else
                {
                $pagina_actual = $_GET['page'] ?? 1;
                $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

                if (!$pagina_actual || $pagina_actual < 1)
                {
                    header('Location: /tipos-curso-buscar?page=1');
                }
                $parametrosFiltro = http_build_query([
                    'columna' => $columna_Seleccionada,
                    'dato' => $registro
                ]);

                $registros_por_pagina = 3;
                // Contar cuántos registros coinciden
                $total = Tipo_Curso_Detalles::whereAllCount($columna_Seleccionada, $registro);
                $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total, $parametrosFiltro);
                $offset = $paginacion->offset();

                // Detectar si es una columna numérica
                $esNumerico = $columna_Seleccionada === 'tipos_curso.id';
                $registroSanitizado = $esNumerico ? intval($registro) : addslashes($registro);

                $where = $esNumerico 
                    ? "{$columna_Seleccionada} = {$registroSanitizado}"
                    : "{$columna_Seleccionada} LIKE '%{$registroSanitizado}%'";

                $query = "SELECT tipos_curso.id, tipos_curso.nombre_curso, modulos.nombre_modulo
                        FROM tipos_cursos
                        LEFT OUTER JOIN modulos ON tipos_curso.modulo_id = modulos.id
                        WHERE {$where}
                        ORDER BY tipos_curso.id DESC
                        LIMIT {$registros_por_pagina} OFFSET {$offset}";
                $tipos_curso = Tipo_Curso_Detalles::SQL($query);
                if (!$tipos_curso)
                {
                    Tipo_Curso::setAlerta('error', 'Registro(s) NO encontrado(s)');
                } else
                    {
                    Tipo_Curso::setAlerta('exito', 'Registro(s) encontrado(s)');
                    }
                }
            } else
                {
                    Tipo_Curso::setAlerta('error', 'Llene los datos para buscar');
                }
        }
        $alertas = Tipo_Curso::getAlertas();
        $router->render('/tipos-curso/buscar',[
            'titulo_pagina' => 'Tipos de cursos',
            'sidebar_nav' => 'Tipos Curso ',            
            'registro'=>$registro,
            'alertas'=>$alertas,
            'tipos_curso' => $tipos_curso,
            'columnasDB' =>$columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            //'paginacion' => is_object($paginacion) ? $paginacion->paginacion() : ''            

        ]);
    }

    
}