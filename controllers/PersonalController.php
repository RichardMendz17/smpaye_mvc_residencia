<?php

namespace Controllers;

use MVC\Router;
use Model\Personal;
use Model\BitacoraEventos;
use Classes\Paginacion;


class PersonalController {

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
            header('Location: /personal?page=1');
        }

        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;
        // Traemos el total de registros
        $total = Personal::total();
        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        if($paginacion->total_paginas() < $pagina_actual)
        {
            header('Location: /personal?page=1');
        }
        $personal = Personal::paginar($registros_por_pagina, $paginacion->offset());

        $alertas = Personal::getAlertas();        
        $router->render('personal/index', [
            'titulo_pagina' => 'Personal',
            'sidebar_nav' => 'Personal',              
            'personal'=> $personal,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion()          
        ]);
    }

    public static function buscar(Router $router)
    {
        $id = validarORedireccionar('/personal-buscar');
        isAuth();

        $alertas = [];
        $personal = null;
        $paginacion = null;

        $columnasDB = Personal::obtenerColumnas();
        $registro = $_GET['dato'] ?? '';
        $columna_Seleccionada = $_GET['columna'] ?? '';

        if (isset($_GET['columna']) && isset($_GET['dato']))
        { 
            if ($columna_Seleccionada !== '' && $registro !== '')
            {
                // Verificar que la columna sea válida
                if (!in_array($columna_Seleccionada, $columnasDB))
                {
                    Personal::setAlerta('error', 'Columna no válida');
                } else
                    {
                    $pagina_actual = $_GET['page'] ?? 1;
                    $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
                    if(!$pagina_actual || $pagina_actual < 1)
                    {
                        header('Location: /personal-buscar?page=1');
                    }
                    $parametrosFiltro = http_build_query([
                        'columna' => $columna_Seleccionada,
                        'dato' => $registro
                    ]);

                    $registros_por_pagina = 3;

                    // Contar cuántos registros coinciden
                    $total = Personal::whereAllCount($columna_Seleccionada, $registro);
                    $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total, $parametrosFiltro);
                    $offset = $paginacion->offset();
                    if($paginacion->total_paginas() < $pagina_actual)
                    {
                        header('Location: /personal-buscar?page=1');
                    }
                    $personal = Personal::whereAllPaginado($columna_Seleccionada, $registro, $registros_por_pagina, $offset);

                    if (!$personal) 
                    {
                        Personal::setAlerta('error', 'Registro(s) NO encontrado(s)');
                    } else 
                        {
                        Personal::setAlerta('exito', 'Registro(s) encontrado(s)');
                        }
                    }
            } else 
                    {
                    Personal::setAlerta('error', 'Llene los datos para buscar');
                    }
        }

        $alertas = Personal::getAlertas();
        $router->render('/personal/buscar', [
            'registro' => $registro,
            'alertas' => $alertas,
            'personal' => $personal,
            'columnasDB' => $columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            'titulo_pagina' => 'Buscar Personal',
            'sidebar_nav' => 'Personal',
            'paginacion' => is_object($paginacion) ? $paginacion->paginacion() : ''
        ]);
    }
  


    public static function crear(Router $router)
    {
        $personal = new Personal();
        $alertas = [];
        isAuth();
        $alertas = Personal::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $personal = new Personal($_POST['personal']);
            $personal->sincronizar($_POST);
            $alertas = $personal->validar();
            if(empty($alertas))
            {
                $verificar = Personal::where('id', $personal->id);
                if($verificar)
                {
                    $alertas['error'][] = 'La matricula del personal ya esta registrado con otro registro';
                } else 
                    {
                    $personal->guardarPersonal();
                    $tabla = 'personal';
                    $id_personal = $personal->id;
                    /* 
                        Si resultado es true nos devolvera el id del registro guardado 
                        y accederemos a ese valor de la siguiente manera
                        $resultado['id'];

                    */
                        $_SESSION['mensaje_exito'] = 'El personal fue creado correctamente.';                    
                        $evento = new BitacoraEventos;
                        $evento->eventos(1, $id_personal, $tabla);
                        header('Location: /personal');
                        exit;
                    }
            }
        }
        $router->render('/personal/crear', [
            'personal' => $personal,
            'alertas' => $alertas,
            'titulo_pagina' => 'Añadir personal',
            'sidebar_nav' => 'Personal',
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/personal');
        $alertas = [];
        isAuth();
        $personal = Personal::find($id);
        if ($personal == false)
        {
            header("Location: /personal");
        }
        $alertas = Personal::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $args = $_POST['personal'];
            $personal->sincronizar($args);
            //debuguear($docente);
            $alertas = $personal->validar();
            if(empty($alertas))
            {
                if($id == $personal->id)
                {
                    $personal->actualizar();
                    $tabla = 'personal';
                    $id_personal = $personal->id;
                    if ($id_personal)
                    {
                        $_SESSION['mensaje_exito'] = 'El personal fue actualizado correctamente.';                    
                        $evento = new BitacoraEventos;
                        $evento->eventos(2, $id_personal, $tabla);
                        header('Location: /personal-buscar?columna=id&dato=' . $personal->id);
                        exit;
                    } 
                }
                else {
                    $personal->id = $id;
                    $alertas['error'][] = 'No es posible modificar el numero de control cuando se actualizan datos del alumno';
                }
            }

        }
        $alertas = Personal::getAlertas();
        $router->render('/personal/actualizar',[
            'personal'=>$personal,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Personal',
            'sidebar_nav' => 'Personal',
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/personal_institucional');
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //Validar id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if($id)
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo))
                {
                    $personal_institucional = personal_institucional::find($id);
                    $resultado = $personal_institucional->eliminar();
                    $tabla = 'personal_institucional';
                    $id_personal_institucional = $personal_institucional->id;
                    if (!campoVacio($resultado))
                    {
                    $_SESSION['mensaje_exito'] = 'El docente fue eliminado correctamente.';                    
                    $evento = new BitacoraEventos;
                    $evento->eventos(3, $id_personal_institucional, $tabla);
                    header("Location: /personal_institucional");                       
                    }  else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el personal, probablemente este siendo utilizado por algun registro.';
                        header("Location: /personal_institucional");
                        exit;
                        } 
                }
            }
        }
    }    

}