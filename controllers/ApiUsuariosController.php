<?php 
namespace Controllers;

use Model\Usuario;


class ApiUsuariosController
{
    public static function cambiar_password()
    {
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $usuario = Usuario::where('id', $_POST['id_usuario']);
            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);
            //debuguear($usuario);
             if (!campoVacio($usuario))
            {
                $usuario->password = $usuario->password_nuevo;
                //  Eliminar propiedades no necesarios
                unset($usuario->password_actual);
                unset($usuario->password_nuevo);
                // Hashear el nuevo password
                $usuario->hashPassword();
                //Actualizar
                $resultado = $usuario->guardar();
            echo json_encode([
                'resultado' => $resultado,
                'mensaje' => $resultado ? 'Contraseña actualizada correctamente' : 'No se pudo actualizar la contraseña',
                'tipo' => $resultado ? 'exito' : 'error'
            ]);
             
                
            }
        }

    }
}
?>