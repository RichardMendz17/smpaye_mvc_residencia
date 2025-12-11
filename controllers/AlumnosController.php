<?php

namespace Controllers;

use MVC\Router;
use Model\Alumno;
use Model\Carrera;
use Model\AlumnoDetalles;
use Model\BitacoraEventos;
use Classes\Paginacion;


class AlumnosController {

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
            header('Location: /alumnos?page=1');
        }

        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;
        // Traemos el total de registros
        $total = Alumno::total();

        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        $alumnos = Alumno::paginar($registros_por_pagina, $paginacion->offset());

        $query = "SELECT alumnos.id, alumnos.nombre_Alumno, alumnos.apellido_Paterno, alumnos.apellido_Materno, alumnos.comentarios, alumnos.telefono, alumnos.correo_institucional, alumnos.genero, carreras.nombre_Carrera ";
        $query .= "FROM alumnos ";
        $query .= "LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id ";
        $query .= "ORDER BY alumnos.id DESC ";
        $query .= "LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";
        $alumnosDetalles = AlumnoDetalles::SQL($query);
        $alertas = Alumno::getAlertas();      
        $router->render('alumnos/index', [
            'titulo_pagina' => 'Alumnos',
            'sidebar_nav' => 'Alumnos',
            'alertas'=>$alertas,
            'alumnos'=>$alumnos,
            'alumnosDetalles'=>$alumnosDetalles,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function buscar(Router $router)
    {
        $id = validarORedireccionar('/alumnos-buscar');
        isAuth();

        $alertas = [];
        $alumnos = null;
        $paginacion = null;

        $columnasDB = AlumnoDetalles::obtenerColumnasAlias();
        $registro = $_GET['dato'] ?? '';
        $columna_Seleccionada = $_GET['columna'] ?? '';

        if (isset($_GET['columna']) && isset($_GET['dato'])) 
        {
            if ($columna_Seleccionada !== '' && $registro !== '')
            {
                // Verificar que la columna sea válida
                if (!in_array($columna_Seleccionada, $columnasDB))
                {
                    Alumno::setAlerta('error', 'Columna no válida');
                } else
                    {
                    $pagina_actual = $_GET['page'] ?? 1;
                    $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

                    if (!$pagina_actual || $pagina_actual < 1)
                    {
                        header('Location: /alumnos-buscar?page=1');
                    }
                    $parametrosFiltro = http_build_query([
                        'columna' => $columna_Seleccionada,
                        'dato' => $registro
                    ]);

                    $registros_por_pagina = 3;
                    // Contar cuántos registros coinciden
                    $total = AlumnoDetalles::contarFiltrados($columna_Seleccionada, $registro);       
                    $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total, $parametrosFiltro);
                    $offset = $paginacion->offset();

                    // Detectar si es una columna numérica
                    $esNumerico = $columna_Seleccionada === 'alumnos.id';
                    $registroSanitizado = $esNumerico ? intval($registro) : addslashes($registro);

                    $where = $esNumerico 
                        ? "{$columna_Seleccionada} = {$registroSanitizado}"
                        : "{$columna_Seleccionada} LIKE '%{$registroSanitizado}%'";

                    $query = "SELECT alumnos.id, alumnos.nombre_Alumno, alumnos.apellido_Paterno, alumnos.apellido_Materno, alumnos.comentarios, alumnos.telefono, alumnos.correo_institucional, alumnos.genero,
                    carreras.nombre_Carrera
                            FROM alumnos
                            LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id
                            WHERE {$where}
                            ORDER BY alumnos.id DESC
                            LIMIT {$registros_por_pagina} OFFSET {$offset}";

                    $alumnos = AlumnoDetalles::SQL($query);

                    if (!$alumnos) 
                    {
                        Alumno::setAlerta('error', 'Registro(s) NO encontrado(s)');
                    } else 
                        {
                        Alumno::setAlerta('exito', 'Registro(s) encontrado(s)');
                        }
                    }
            } else  
                {
                    Alumno::setAlerta('error', 'Llene los datos para buscar');
                }
        }
        $alertas = Alumno::getAlertas();
        $router->render('/alumnos/buscar', [
            'registro' => $registro,
            'alertas' => $alertas,
            'alumnos' => $alumnos,
            'columnasDB' => $columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            'titulo_pagina' => 'Buscar Alumno',
            'sidebar_nav' => 'Alumnos',
            'paginacion' => is_object($paginacion) ? $paginacion->paginacion() : ''            
        ]);
    }
    

    public static function crear(Router $router)
    {
        $alumno = new Alumno;
        $carreras = Carrera::all();
        $alertas = [];

        isAuth();
        $alertas = Alumno::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $alumno = new Alumno($_POST['alumno']);
            $alumno->sincronizar($_POST);
            $alertas = $alumno->validar();
            if(empty($alertas))
            {
                $verificar = Alumno::where('id', $alumno->id);
                if($verificar)
                {
                    $alertas['error'][] = 'El  numero de control ya esta registrado';
                } else 
                    {
                        $alumno->guardarAlumno();
                        $tabla = 'alumnos';
                        $id_alumno = $alumno->id;
                        if ($id_alumno)
                        {
                            $_SESSION['mensaje_exito'] = 'El alumno fue creado correctamente.';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(1, $id_alumno, $tabla);
                            header('Location: /alumnos');
                            exit;
                        }
                    }   
            }
        }
        $router->render('/alumnos/crear', [
            'alumno' => $alumno,
            'carreras'=>$carreras,
            'alertas' => $alertas,
            'titulo_pagina' => 'Crear Alumno',
            'sidebar_nav' => 'Alumnos',


        ]);
    }

    public static function importar(Router $router)
    {

        isAuth();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $archivotmp = $_FILES['dataAlumnos']['tmp_name'];
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
        
        $router->render('/alumnos/importar-alumnos', [
            'titulo_pagina' => 'Importar Alumnos',
            'sidebar_nav' => 'Alumnos',
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/alumnos');
        $alertas = [];
        isAuth();
        $alumno = Alumno::find($id);
        $carreras = Carrera::all();

        if ($alumno == false)
        {
            header("Location: /alumnos");
        }

        $alertas = Alumno::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $args = $_POST['alumno'];
            $alumno->sincronizar($args);
            //debuguear($alumno);
            $alertas = $alumno->validar();
            if(empty($alertas)){
                if($id == $alumno->id)
                {
                    $alumno->actualizarAlumno();
                    $tabla = 'alumnos';
                    $id_alumno = $alumno->id;
                    if ($id_alumno)
                    {
                        $_SESSION['mensaje_exito'] = 'El alumno fue actualizado correctamente.';                    
                        $evento = new BitacoraEventos;
                        $evento->eventos(2, $id_alumno, $tabla);
                        header('Location: /alumnos-buscar?columna=alumnos.id&dato=' . $alumno->id);
                        exit;
                    } 
                } else 
                    {
                    $alumno->id = $id;
                    $alertas['error'][] = 'No es posible actualizar el Número de control de un registro existente';
                    var_dump($alertas);
                    }
            }

        }
        $router->render('/alumnos/actualizar',[
            'alumno'=>$alumno,
            'carreras'=>$carreras,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Alumno',
            'sidebar_nav' => 'Alumnos',
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/alumnos');
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
                    $alumno = Alumno::find($id);
                    $resultado =  $alumno->eliminar();
                    if ($resultado !== null && $resultado !== false)
                    {
                        $tabla = 'alumnos';
                        $id_registro = $alumno->id;
                        if ($id_registro !== null)
                        {
                        $_SESSION['mensaje_exito'] = 'El alumno fue eliminado correctamente.';
                        $evento = new BitacoraEventos;
                        $evento->eventos(3, $id_registro, $tabla);
                        header("Location: /alumnos");
                        exit;
                        }
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el alumno, probablemente este siendo utilizada por un registro.';
                        header("Location: /alumnos");
                        exit;
                        }                      
                }
            }
        }
    }    

}