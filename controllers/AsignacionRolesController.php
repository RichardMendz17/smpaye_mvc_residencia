<?php

namespace Controllers;

use MVC\Router;
use Model\Carrera;
use Model\BitacoraEventos;
use Classes\Paginacion;
use Model\AsignacionRol;
use Model\Personal;
use Model\PersonalRolDetalles;
use Model\Rol;
use Model\Usuario;

class AsignacionRolesController {
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
            header('Location: /asignacion-roles?page=1');
        }
        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;
        // Construimos la consulta base
        $query_base  = "SELECT DISTINCT asignacion_roles.id, asignacion_roles.id_personal, asignacion_roles.id_rol,   ";
        $query_base .= "CONCAT(personal.nombre, ' ', personal.apellido_Paterno, ' ', personal.apellido_Materno) AS nombre_personal, ";
        $query_base .= "roles.rol AS nombre_rol "; 
        $query_base .= "FROM asignacion_roles ";
        $query_base .= "LEFT OUTER JOIN personal ON asignacion_roles.id_personal = personal.id ";
        $query_base .= "LEFT OUTER JOIN roles ON asignacion_roles.id_rol = roles.id ";

        $total = PersonalRolDetalles::SQLContar($query_base);
        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);

        // Consulta paginada
        $query_base .= " LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";
        // Obtenemos el total de los cursos por periodos
        $roles_personal = PersonalRolDetalles::SQL($query_base);

        $alertas = PersonalRolDetalles::getAlertas();
        $router->render('asignacion_rol/index',[
            'titulo_pagina' => 'Asignacion de Roles',
            'sidebar_nav' => 'Asignacion Roles',
            'roles_personal'=>$roles_personal,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function crear(Router $router)
    {
        $personal_nuevo_rol = new AsignacionRol;
        $personal = Personal::all();
        $roles = Rol::all();
        $alertas = [];
        isAuth();
        $alertas = AsignacionRol::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $personal_nuevo_rol = new AsignacionRol($_POST['asignacion_rol']);
            $personal_nuevo_rol->sincronizar($_POST);
            $alertas = $personal_nuevo_rol->validar();
            if(empty($alertas))
            {
                $verificar = AsignacionRol::buscarPorMultiples(
                    ['id_personal', 'id_rol'], 
                    [$personal_nuevo_rol->id_personal, $personal_nuevo_rol->id_rol]);
                if($verificar)
                {
                    $alertas['error'][] = 'El Personal ya cuenta con el rol que desea asignar, no hace falta reasignar el rol';
                } else 
                    {
                    $resultado = $personal_nuevo_rol->guardar();
                    $tabla = 'asignacion_rol';
                    $id_registro = $resultado['id'];                    
                    if (!campoVacio($id_registro))
                    {
                        $_SESSION['mensaje_exito'] = 'El Nuevo rol fue asignado al personal seleccionado correctamente.';                    
                        $evento = new BitacoraEventos;
                        $evento->eventos(1, $id_personal_nuevo_rol, $tabla);
                        header('Location: /asignacion-roles');
                        exit;
                    }
                }
            }
        }
        $router->render('asignacion_rol/crear', [
            'personal_nuevo_rol' => $personal_nuevo_rol,
            'personal'=>$personal,
            'roles'=>$roles,
            'alertas' => $alertas,
            'titulo_pagina' => 'Asignar Rol',
            'sidebar_nav' => 'Asignacion Roles',


        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar(' /asignacion-roles');
        $alertas = [];
        isAuth();
        $carrera = Carrera::find($id);
        if ($carrera == false) 
        {
            header("Location: /carreras");
        }
        // Ejecutar el código despues de que el usuario envia el formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Asignar los valores
            $args = $_POST['carrera'];
            //Sincronizar objeto en memoria con lo que el usuario escribió
            $carrera->sincronizar($args);
            // Validación
            $alertas = $carrera->validar();
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
        $alertas = Carrera::getAlertas();
        $router->render('/carreras/actualizar',[
            'carrera'=>$carrera,
            'alertas' => $alertas,
            'titulo_pagina' => 'Actualizar Carrera',
            'sidebar_nav' => 'Carreras',            
        ]);
    }

    public static function eliminar()
    {
        $id = validarORedireccionar('/asignacion-roles?page=1');
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if(!campoVacio($id))
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo))
                {
                    $personal_rol = AsignacionRol::find($id);
                    // Obtenemos los valores para eliminar el rol por defecto en el usuario si acaso lo tiene
                    $id_personal = $personal_rol->id_personal;
                    $id_rol_personal = $personal_rol->id_rol;

                    $resultado = $personal_rol->eliminar();                
                    if (!campoVacio($resultado))
                    {
                        // Ahora tenemos que verificar si el usuario tenia ese rol por defecto su modelo de usuario
                        // Buscaremos el registro por tipo de usuario = 2 y el id del personal
                        $usuario_personal = Usuario::BuscarPorMultiples(
                            [
                                'tipo_usuario',
                                'persona_id',
                                'rol'
                            ],
                            [
                                2,
                                $id_personal,
                                $id_rol_personal
                            ]);
                            if (!campoVacio($usuario_personal)) 
                            {
                                $usuario_personal->rol = null;
                                $resultado = $usuario_personal->guardar();
                                $_SESSION['mensaje_exito'] = 'El Rol por defecto fue retirado del personal correctamente.';
                                // ahora tenemos que validar si elimino el rol del usuario actual en sesion 
                                // que verifique si el rol que borro es el que tenia asignado la sesion y si si enviarlo a verificar
                                // rol
                            }

                        $tabla = 'asignacion_roles';
                        $id_registro = $personal_rol->id;
                        if (!campoVacio($id_registro))
                        {
                            $_SESSION['mensaje_exito'] = 'El Rol fue retirado del personal correctamente.';
                            $evento = new BitacoraEventos;
                            $evento->eventos(3, $id_registro, $tabla);
                            header("Location: /asignacion-roles");
                        }
                    } else 
                        {
                            $_SESSION['mensaje_error'] = 'No fue posible retirarle el rol al personal, probablemente este siendo utilizada por un registro.';
                            header("Location: /asignacion-roles");                              
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