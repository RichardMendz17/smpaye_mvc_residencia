<?php 

namespace Controllers;

use Model\Personal;
use Model\PersonalRolSeleccionado;
use Model\Rol;
use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas))
            {
                // Verificar que el usuario exista
                $usuario = new Usuario((array) Usuario::where('email', $usuario->email));
                if (!$usuario)
                {
                    Usuario::setAlerta('error', 'El usuario No existe');
                    $_SESSION['login'] = false; // Fuerza logout
                } else 
                    {
                        // El usuario existe
                        if (password_verify($_POST['password'], $usuario->password)) 
                        {
                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;
                            $_SESSION['persona_id'] = $usuario->persona_id;
                            $_SESSION['cambiando_rol'] = false;
                            $_SESSION['tipo_usuario'] = (int)$usuario->obtenerTipoUsuario();
                            $_SESSION['nombre'] = $usuario->obtenerNombreReal();
                            // Posteriormente de capturar los datos previos
                            // Verificamos el tipo de usuario si es alumno o personal
                            header('location:/verificar-tipo-usuario');
                            exit;
                            // Identificamos el tipo de usuario
                        } else 
                            {
                                Usuario::setAlerta('error', 'Password Incorrecta');
                                $_SESSION = []; 
                                session_destroy();
                            }
                    }
            }

        }
        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /');
    }

}