<?php

namespace Controllers;

use MVC\Router;
use Model\Aula;
use Model\BitacoraEventos;
use Classes\Paginacion;
use Model\AsignacionRol;
use Model\PersonalRolDetalles;
use Model\PersonalRolSeleccionado;
use Model\Usuario;

class SeleccionarRolController {

    public static function index(Router $router)
    {
        isAuth();
        $rol_seleccionado = new PersonalRolSeleccionado;
        $alertas = [];
        // Si trata de acceder a este controlador con la variable de cambiando_rol en false
        // lo redirigimos  ala parte de cambiar-rol que se encarga de poner la variable en true
        if ($_SESSION['cambiando_rol'] === false)
        {
            header('location: /cambiar-rol');
            exit;
        }

        // capturamos el id del usuario
        $id_usuario = $_SESSION['persona_id'];

        // Construimos la consulta base
        $query_base  = "SELECT DISTINCT asignacion_roles.id, asignacion_roles.id_personal, asignacion_roles.id_rol, ";
        $query_base .= "CONCAT(personal.nombre, ' ', personal.apellido_Paterno, ' ', personal.apellido_Materno) AS nombre_personal, ";
        $query_base .= "roles.rol AS nombre_rol "; 
        $query_base .= "FROM asignacion_roles ";
        $query_base .= "LEFT OUTER JOIN personal ON asignacion_roles.id_personal = personal.id ";
        $query_base .= "LEFT OUTER JOIN roles ON asignacion_roles.id_rol = roles.id ";
        $query_base .= "WHERE asignacion_roles.id_personal = {$id_usuario}";

        $usuario_roles = PersonalRolDetalles::SQL($query_base);
        //debuguear($usuario_roles);
        $alertas = PersonalRolSeleccionado::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Se recomienda factorizar esta parte como se hizo al crear curso en el controlador
            // de actividades extraescolares dashboard controller metodo crear
            if (isset($_POST['rol']['id_rol_predeterminado']) && !campoVacio($_POST['rol']['id_rol_predeterminado'])) 
            {
                $rol_elegido = $_POST['rol']['id_rol_predeterminado'];
                $_POST['rol']['id_rol'] = $rol_elegido;
                $predeterminado = true;
            }
            if (isset($_POST['rol']['quitar_id_rol_predeterminado']) && !campoVacio($_POST['rol']['quitar_id_rol_predeterminado'])) 
            {
                $rol_elegido = $_POST['rol']['quitar_id_rol_predeterminado'];
                $_POST['rol']['id_rol'] = $rol_elegido;
                $retirar_rol_predeterminado = true;
            }   else    
                {

                   // echo 'Eligio un rol temporal para esta sesion';
                } 
            $rol_seleccionado = new PersonalRolSeleccionado($_POST['rol']);
            $rol_seleccionado->sincronizar($_POST);
            $alertas = $rol_seleccionado->validar();
            //debuguear($retirar_rol_predeterminado);
            if(empty($alertas))
            {

                // Verificamos que el usuario tenga el rol seleccionado
                $verificar = PersonalRolSeleccionado::buscarPorMultiples(
                    [// Columnas
                            'id_rol', 
                            'id_personal'
                    ], 
                    [// Valores ordenados respectivamente
                        $rol_seleccionado->id_rol,
                        $rol_seleccionado->id_personal
                    ]);
                    //debuguear($verificar);
                if(!campoVacio($verificar))
                { // ahora depende de la opcion que eligio validamos que proceso hacemos
                    $usuario = Usuario::find($_SESSION['id']);

                    if (isset($retirar_rol_predeterminado) && $retirar_rol_predeterminado === true ) 
                    {
                        $usuario->rol = null;
                        $resultado= $usuario->guardar();
                        //debuguear($resultado);
                        if (!campoVacio($resultado))
                        {
                            unset($_SESSION['rol_por_defecto']);
                            $retirar_rol_predeterminado = false;
                            $alertas['exito'][] = 'Rol predeterminado retirado correctamente';
                        }

                    }
                    if (isset($predeterminado) && $predeterminado === true) 
                    {
                        $usuario->rol = $rol_elegido;
                        //debuguear($usuario);
                        $resultado= $usuario->guardar();
                        if (!campoVacio($resultado))
                        {
                            $_SESSION['rol_por_defecto'] = $rol_elegido;
                            $predeterminado = false;
                            $alertas['exito'][] = 'Guardado como predeterminado';
                        }
                    } 
                    if (!isset($predeterminado) && !isset($retirar_rol_predeterminado))
                    {
                            $_SESSION['rol'] =  (int)$rol_seleccionado->id_rol;
                            $_SESSION['nombre_rol'] = $rol_seleccionado->obtenerNombreRol();
                            $_SESSION['cambiando_rol'] = false;
                            header( 'Location: /dashboard');
                            exit;
                    }

                } else 
                    {
                        $alertas['error'][] = 'Seleccione un rol valido';
                    } 
            }
        }
        $router->render('seleccionar_rol/index',[
            'titulo_pagina' => 'Roles del usuario',
            'sidebar_nav' => 'Seleccionar Rol',
            'usuario_roles'=>$usuario_roles,
            'alertas'=>$alertas

        ]);
    }
    

    public static function quitar_rol()
    {
        isAuth();
        $tipo_usuario = $_SESSION['tipo_usuario'];
        switch ($tipo_usuario) {
            case 1:
                // Un usuario de tipo alumno no puede acceder a este metodo
                // lo redirigimos
                header('Location: /perfil');
                exit;               
            break;
            case 2:
                // Si el usuario de tipo 2 tiene solo un rol lo redirigimos a verificar rol
                // Ya que no tiene caso que vuelva a elegir el mismo rol y haga el mismo bucle
                $total_roles = $_SESSION['total_roles'];
                if ($total_roles === 1)
                {
                    header('location: /verificar-rol');
                    exit;
                } 
                else 
                {
                    // quitamos el rol para que el usuario pueda elegir otro 
                    $_SESSION['rol']= null;
                    $_SESSION['nombre_rol'] = null;
                    $_SESSION['cambiando_rol'] = true;
                    header('location: /seleccionar-rol');
                    exit;
                }
            break;            
            default:
                # code...
                break;
        }


    }
    
}