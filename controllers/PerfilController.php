<?php

namespace Controllers;

use MVC\Router;
use Model\Aulas;
use Model\Cursos;
use Model\Niveles;
use Model\Usuario;
use Model\Docentes;
use Model\Periodos;
use Classes\Paginacion;
use Model\AlumnoCursoDetalles;
use Model\BitacoraEventos;
use Model\CursosDetalles;

class PerfilController 
{ 
        public static function perfil(Router $router)
    {
        isAuth();
        $alertas= [];
        $usuario = Usuario::find($_SESSION['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();
            if (empty($alertas))
            {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario && $existeUsuario->id !== $usuario->id)
                {
                    // Mensaje de la existencia de otro usuario con el mismo email
                    // Por lo tanto no es posible guardar el registro con un email duplicado
                    Usuario::setAlerta('error', 'Email no válido, el email ya pertenece a otro usuario');
                    $alertas = $usuario->getAlertas();
                } else
                    {
                    // guardar el usuario
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                    //Asignar el nombre nuevo ala barra
                    $_SESSION['nombre'] = $usuario->nombre;
                    }   
            }
        }
        $router->render('perfil/perfil', [
            'titulo_pagina' => 'Perfil',
            'sidebar_nav' => 'Perfil',     
            'usuario' => $usuario,
            'alertas' => $alertas,
        ]);
    }

    public static function cambiar_password(Router $router)
    {
        isAuth();
        $alertas= [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $usuario =  Usuario::find($_SESSION['id']);
            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if (empty($alertas))
            {
                $resultado = $usuario->comprobar_password();
                if ($resultado) 
                {
                    $usuario->password = $usuario->password_nuevo;

                    //  Eliminar propiedades no necesarios
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado)
                    {
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else
                    {
                        Usuario::setAlerta('error', 'Password Actual Incorrecto');
                        $alertas = $usuario->getAlertas();
                    }
            }
        }
        $router->render('perfil/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'titulo_pagina' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }
}

?>