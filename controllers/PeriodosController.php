<?php

namespace Controllers;

use MVC\Router;
use Model\Periodo;
use Model\BitacoraEventos;
use Classes\Paginacion;
class PeriodosController {

    public static function index(Router $router)
    {
        $alertas = [];
        isAuth();
        $rol = $_SESSION['rol'];

        // Obtenemos la pagina actual
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        //Validamos que la pagina actual exista y que no sea menor a 1
        if (!$pagina_actual || $pagina_actual < 1)
        {
            header('Location: /periodos?page=1');
        }
        
        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;
        // Traemos el total de registros
        $total = Periodo::total();

        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);

        $periodos = Periodo::paginar($registros_por_pagina, $paginacion->offset());
            switch ($rol) {
                case 0: // Admin
                    $crear = '/periodos-crear';
                    $buscar = '/periodos-buscar';
                break;
                default: // Admin
                    $crear = null;
                    $buscar =null ;
                break;
            }


    
    $alertas = Periodo::getAlertas();        
        $router->render('periodos/index', [
            'titulo_pagina' => 'Periodos',
            'sidebar_nav' => 'Periodos',              
            'periodos'=>$periodos,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion(),
            'crear' => $crear,
            'buscar'=> $buscar       
        ]);
    }

    public static function crear(Router $router)
    {
        // Cada que se cree un nuevo per
        $alertas = [];
        isAuth();

        $periodo = new Periodo;
        $alertas = [];
        $alertas = Periodo::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $periodo = new Periodo($_POST['periodo']);
            $periodo->sincronizar($_POST);
            $alertas = $periodo->validar();
            if(empty($alertas))
            {
                $resultado = $periodo->verificar_meses_Periodo('meses_Periodo', $periodo->meses_Periodo, 'year' ,$periodo->year);
                if($resultado === false)
                {
                    $alertas['error'][] = 'El periodo ya esta registradO';
                } else 
                    {
                    $resultado = $periodo->guardar();
                    $tabla = 'periodos';
                    $id_registro = $resultado['id'];
                        if ($id_registro)
                        {
                            $_SESSION['mensaje_exito'] = 'El Periodo de los meses: "' . $periodo->meses_Periodo . '" y año: "'. $periodo->year .'" fue creado correctamente.';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(1, $id_registro, $tabla);
                            header('Location: /periodos');
                            exit;
                        }
                    }
            }
        }
        $router->render('/periodos/crear', [
            'titulo_pagina' => 'Periodos',
            'sidebar_nav' => 'Periodos',               
            'periodo' => $periodo,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router)
    {
        $alertas = [];
        isAuth();
        $id = validarORedireccionar('/periodos-buscar');
        $alertas = [];
        $periodo = Periodo::find($id);
        if ($periodo == false)
        {
            header("Location: /periodos-buscar");
        }
        $alertas = Periodo::getAlertas();
        // Ejecutar el código despues de que el usuario envia el formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Asignar los valores
            $args = $_POST['periodo'];
            //Sincronizar objeto en memoria con lo que el usuario escribió
            $periodo->sincronizar($args);
            // Validación
            $alertas = $periodo->validar();
            if(empty($alertas))
            {
                    $periodo->guardar();
                    $tabla = 'periodos';
                    $id_registro = $periodo->id;
                        if ($id_registro)
                        {
                            $_SESSION['mensaje_exito'] = 'El Periodo fue actualizado correctamente';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(1, $id_registro, $tabla);
                            header('Location: /periodos');
                            exit; // OBLIGATORIO para que se haga la redirección correctamente
                        }                    

            }
        }
        $router->render('/periodos/actualizar',[
            'titulo_pagina' => 'Periodos',
            'sidebar_nav' => 'Periodos',            
            'periodo'=>$periodo,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/periodos');
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
                    $periodo = Periodo::find($id);
                    $resultado =  $periodo->eliminar();
                    if ($resultado !== null && $resultado !== false)
                    {
                        $tabla = 'periodos';
                        $id_registro = $periodo->id;
                        if ($id_registro !== null)
                        {
                        $_SESSION['mensaje_exito'] = 'El periodo fue eliminado correctamente.';
                        $evento = new BitacoraEventos;
                        $evento->eventos(3, $id_registro, $tabla);
                        header("Location: /periodos");
                        }
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el periodo, probablemente este siendo utilizada por un registro.';
                        header("Location: /periodos");                                    
                        }                      
                }
            }
        }
    }    


    public static function buscar(Router $router)
    {
        $id = validarORedireccionar('/periodos-buscar');
        isAuth();
        $alertas = []; 
        $periodos =  NULL;
        $paginacion = null;

        //Obtener las columnas para elegir por cual buscar
        $columnasDB = Periodo::obtenerColumnas();
        $registro = $_GET['dato'] ?? '';
        $columna_Seleccionada = $_GET['columna'] ?? '';

        if (isset($_GET['columna']) && isset($_GET['dato'])) 
        {
            if ($columna_Seleccionada !== '' && $registro !== '')
            {
            // Verificar que la columna sea válida
            if (!in_array($columna_Seleccionada, $columnasDB)) 
            {
                Periodo::setAlerta('error', 'Columna no válida');
            } else 
                {
                $pagina_actual = $_GET['page'] ?? 1;
                $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

                if (!$pagina_actual || $pagina_actual < 1) {
                    header('Location: /periodos-buscar?page=1');
                }
                $parametrosFiltro = http_build_query([
                    'columna' => $columna_Seleccionada,
                    'dato' => $registro
                ]);

                $registros_por_pagina = 3;
                // Contar cuántos registros coinciden
                $total = Periodo::whereAllCount($columna_Seleccionada, $registro);
                $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total, $parametrosFiltro);
                $offset = $paginacion->offset();

                $periodos = Periodo::whereAllPaginado($columna_Seleccionada, $registro, $registros_por_pagina, $offset);
                if (!$periodos)
                {
                    Periodo::setAlerta('error', 'Registro(s) NO encontrado(s)');
                } else 
                    {
                    Periodo::setAlerta('exito', 'Registro(s) encontrado(s)');
                    }
                }
                } else 
                    {
                    Periodo::setAlerta('error', 'Llene los datos para buscar');
                    }
        }
        $alertas = Periodo::getAlertas();
        $router->render('/periodos/buscar',[
            'titulo_pagina' => 'Periodos',
            'sidebar_nav' => 'Periodos',            
            'registro'=>$registro,
            'alertas'=>$alertas,
            'periodos' =>$periodos,
            'columnasDB' =>$columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            'paginacion' => is_object($paginacion) ? $paginacion->paginacion() : ''
        ]);
    }

    
}