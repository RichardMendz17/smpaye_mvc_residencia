<?php

namespace Controllers;

use MVC\Router;
use Model\Carrera;
use Model\BitacoraEventos;
use Classes\Paginacion;

class CarrerasController {
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
        $total = Carrera::total();
        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);

        $carreras = Carrera::paginar($registros_por_pagina, $paginacion->offset());

        $alertas = Carrera::getAlertas();
        $router->render('carreras/index',[
            'titulo_pagina' => 'Carreras',
            'sidebar_nav' => 'Carreras',
            'carreras'=>$carreras,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        isAuth();
        $carrera = new Carrera;
        $alertas = Carrera::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $carrera = new Carrera($_POST['carrera']);
            $carrera->sincronizar($_POST);
            //debuguear($carrera);
            $alertas = $carrera->validar();
            if(empty($alertas))
            {
                $verificar = Carrera::where('nombre_Carrera', $carrera->nombre_Carrera);
                //debuguear($verificar);
                if($verificar)
                {
                    $alertas['error'][] = 'La carrera ya esta registrada';
                } else
                    {
                        $resultado = $carrera->guardar();
                        $tabla = 'carreras';
                        $id_registro = $resultado['id'];
                        if ($id_registro)
                        {
                            $_SESSION['mensaje_exito'] = 'La carrera "' . $carrera->nombre_Carrera . '" fue creada correctamente.';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(1, $id_registro, $tabla);
                            header('Location: /carreras');
                            exit;
                        }
                    }
            }
        }
        $router->render('/carreras/crear',[
            'carrera' => $carrera,
            'alertas' => $alertas,
            'titulo_pagina' => 'Buscar Carrera',
            'sidebar_nav' => 'Carreras',
            
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/carreras');
        $alertas = [];
        isAuth();
        $carrera = Carrera::find($id);

        if ($carrera == false) 
        {
            header("Location: /carreras");
        }
        // Ejecutar el código despues de que el usuario envia el formulario
        $alertas = Carrera::getAlertas();

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Asignar los valores
            $args = $_POST['carrera'];
            //Sincronizar objeto en memoria con lo que el usuario escribió
            $carrera->sincronizar($args);
            // Validación
            $alertas = $carrera->validar();
            //debuguear($alertas);

            if (empty($alertas))
            {
                $verificar = Carrera::where('nombre_Carrera', $carrera->nombre_Carrera);
                if($verificar)
                {
                    $alertas['error'][] = 'La carrera ya esta registrada';
                } else
                    {
                    $carrera->guardar();
                    $tabla = 'carreras';
                    $id_registro = $carrera->id;
                        if ($id_registro)
                        {
                            $_SESSION['mensaje_exito'] = 'La carrera fue actualizada correctamente';                    
                            $evento = new BitacoraEventos;
                            $evento->eventos(2, $id_registro, $tabla);
                            header('Location: /carreras');
                            exit; // OBLIGATORIO para que se haga la redirección correctamente
                        }
                    }
            }
        }
        $router->render('/carreras/actualizar',[
            'carrera'=>$carrera,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Carrera',
            'sidebar_nav' => 'Carreras',            
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/carreras');
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if($id !== false && $id >= 0)
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo))
                {
                    $carrera = Carreras::find($id);                 
                    $resultado = $carrera->eliminar();                
                    if ($resultado !== null && $resultado !== false)
                    {
                        $tabla = 'carreras';
                        $id_registro = $carrera->id;
                        if ($id_registro !== null)
                        {
                        $_SESSION['mensaje_exito'] = 'La carrera fue eliminada correctamente.';
                        $evento = new BitacoraEventos;
                        $evento->eventos(3, $id_registro, $tabla);
                        header("Location: /carreras");
                        }
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar la carrera, probablemente este siendo utilizada por un registro.';
                        header("Location: /carreras");                              
                        }
                }
            }
        }
    }    


    public static function buscar(Router $router){
        $alertas = [];
        isAuth();
        //Obtener las columnas para elegir por cual buscar
        $columnasDB = Carrera::obtenerColumnas();
        $carreras =  NULL;
        $columna_Seleccionada = null;
        $registro =  NULL;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $columna_Seleccionada = $_POST['columna'] ?? '';
            $registro = $_POST['dato'] ?? '';
            if ($columna_Seleccionada == '' || $registro ==  ''){
                Carrera::setAlerta('error', 'Llene los datos correctamente para buscar');
            } else {
                $carreras = Carrera::where($columna_Seleccionada, $registro);
                if(!$carreras){
                    Carrera::setAlerta('error', 'Registro (s)  NO encontrado(s)');
                } else {
                    Carrera::setAlerta('exito', 'Registro (s) encontrado(s)');
                }
            }
        }

        $alertas = Carrera::getAlertas();
        $router->render('/carreras/buscar',[
            'registro'=>$registro,
            'alertas'=>$alertas,
            'carreras' =>$carreras,
            'columnasDB' =>$columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            'titulo_pagina' => 'Buscar Carrera',
            'sidebar_nav' => 'Carreras',              
        ]);
    }

    
}