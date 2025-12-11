<?php

namespace Controllers;

use Model\PersonalRolSeleccionado;
use MVC\Router;
use Model\Rol;
use Model\Usuario;

class VerificarRolController
{
    public static function index(Router $router)
    {
        isAuth();
        $tipo_usuario = $_SESSION['tipo_usuario'];

        switch ($tipo_usuario)
        {
            case 1:
                // para alumno es el rol con el id 1
                // creamos un nuevo objeto y lo buscamos
                $rol = new Rol;
                $rol_del_usuario = $rol::find(1);
                // Asignamos valores a la variable global SESSION
                $_SESSION['rol'] =  (int)$rol_del_usuario->id;
                $_SESSION['nombre_rol'] = $rol_del_usuario->rol;
                header('location: /perfil');
                break;

            case 2:
                // Creamos un objeto de tipo rol
                $rol_del_usuario = new PersonalRolSeleccionado;
                // Verificamos si el usuario tiene un rol predeterminado y asignamos los datos de dicho rol
                // Nuevamente validamos al usuario para verificar el campo de rol
                $usuario = new Usuario;
                $usuario = $usuario::find($_SESSION['id']);

                // Traemos la cantidad total de roles
                $roles_pertenecientes_al_usuario = PersonalRolSeleccionado::belongsTo('id_personal', $_SESSION['persona_id']);
                $total_roles = count($roles_pertenecientes_al_usuario);
                if ($total_roles > 1) 
                {
                    $_SESSION['total_roles'] = $total_roles;
                }
                // Si no tiene un rol por defecto pero solo tiene un rol asignado lo colocamos automaticamente
                if ($total_roles === 1) 
                {
                    // Si solo tiene un rol, lo asignamos automaticamente
                    $unico_rol_del_usuario = $roles_pertenecientes_al_usuario[0];
                    $rol_del_usuario->asignarRolYRedirigir((int)$unico_rol_del_usuario->id_rol);
                }
                // Si tiene un rol por defecto lo asignamos
                if (!campoVacio($usuario->rol))
                {
                    $_SESSION['rol_por_defecto'] = $usuario->rol;
                    $rol_del_usuario->asignarRolYRedirigir((int)$usuario->rol);
                }

                // Si no tiene un rol por defecto pero tiene varios roles lo hacemos elegir uno
                header('location: /seleccionar-rol');
                exit;                                                                 
            break;            
            default:
                # code...
            break;
        }

    }
 
}