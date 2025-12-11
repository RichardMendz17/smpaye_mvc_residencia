<?php

namespace Controllers;

use MVC\Router;
use Model\PersonalRolSeleccionado;

class VerificarTipoUsuarioController
{
    public static function index(Router $router)
    {
        isAuth();
        // Identificamos el tipo de usuario
        $tipo_usuario = $_SESSION['tipo_usuario'];

        switch ($tipo_usuario) {
            //1 Para alumno
            case 1:
                header('Location: /verificar-rol');
                exit;
            break;
            // 2 Para Personal
            case 2:
                header('Location: /verificar-rol');
                exit;
            break;
            // Defaul para un usuario no tiene definido el tipo de usuario
            default:
                echo 'Usuario no válido';
            break;
        }
    }
 
}