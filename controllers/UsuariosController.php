<?php 

namespace Controllers;

use MVC\Router;
use Model\Rol;
use Model\Usuario;
use Model\BitacoraEventos;
use Classes\Paginacion;
use Model\PersonalRolSeleccionado;
use Model\UsuarioDetalle;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UsuariosController
{

    public static function administrar_usuarios(Router $router)
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
        $total = Usuario::total();
        // En base al total de registros y a los registros que deseamos mostrar por pagina vamos a crear un objeto
        // de la clase Paginación que se encargara de realizar toda la logica necesaria

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        $usuarios = Usuario::paginar($registros_por_pagina, $paginacion->offset());
        $query = "SELECT usuarios.id, usuarios.email, usuarios.password, usuarios.persona_id, usuarios.tipo_usuario ";
        $query .= "FROM usuarios ";
        $query .= "LEFT OUTER JOIN roles ON usuarios.rol = roles.id ";
        $query .= "ORDER BY usuarios.id DESC ";
        $query .= "LIMIT {$registros_por_pagina} OFFSET {$paginacion->offset()}";

        $usuariosDetalles = UsuarioDetalle::SQL($query);
        //debuguear($usuariosDetalles);
        $alertas = Usuario::getAlertas();                     
        // Render a la vista
        $router->render('usuario/index', [
            'titulo_pagina' => 'Usuarios',
            'sidebar_nav' => 'Usuarios',
            'alertas'=>$alertas,
            'usuariosDetalles' => $usuariosDetalles,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function buscar(Router $router)
    {
        $alertas = [];
        isAuth();
        //Obtener las columnas para elegir por cual buscar
        $columnasDB = Usuario::obtenerColumnas();
        $aulas =  NULL;
        $columna_Seleccionada = null;
        $registro =  NULL;
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $columna_Seleccionada = $_POST['columna'] ?? '';
            $registro = $_POST['dato'] ?? '';
            if ($columna_Seleccionada == '' || $registro ==  ''){
                Usuario::setAlerta('error', 'Llene los datos correctamente para buscar');
            } else {
                $aulas = Usuario::where($columna_Seleccionada, $registro);
                if(!$aulas){
                    Usuario::setAlerta('error', 'Registro (s)  NO encontrado(s)');
                } else {
                    Usuario::setAlerta('exito', 'Registro (s) encontrado(s)');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('usuario/buscar',[
            'registro'=>$registro,
            'alertas'=>$alertas,
            'aulas' =>$aulas,
            'columnasDB' =>$columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
            'titulo_pagina' => 'Buscar Usuario',
            'sidebar_nav' => 'Usuarios',              
        ]);
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        isAuth();
        $roles = Rol::all();
        $usuario = new Usuario;
        $personal_rol = new PersonalRolSeleccionado;
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario = new Usuario($_POST['usuario']);
            $personal_rol = new PersonalRolSeleccionado($_POST['personal_rol']);
            $usuario->sincronizar($_POST);
            // Verificamos a que tipo de usuario pertenece el rol seleccionado en la variable $personal_rol
            // comparandolo con los siguientes arreglos
            $Roles_Personal = ['0','2','3','4','5','6','7','8','9','10','11','12','13'];
            $RolAlumno = ['1'];
            // En base a esto anterior colocamos el tipo de usuario en el objeto de usuario 
            // correspondiente 
            // 2 Personal
            // 1 Alumno
            if (in_array($personal_rol->id_rol, $Roles_Personal))
            {
               $usuario->tipo_usuario = 2;
            } 
            else if (in_array($personal_rol->id_rol, $RolAlumno)) 
            {   // Asignamos el tipo de usuario y el rol
                $usuario->tipo_usuario = 1;
                $usuario->rol = (int)$personal_rol->id_rol;
            }

            $alertas = $usuario->validarNuevaCuenta();
            if(empty($alertas))
            {
                $existeUsuario = Usuario::where('email', $usuario->email);
            
                if ($existeUsuario) 
                {
                    Usuario::setAlerta('error', 'el usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else 
                    {
                        if ($usuario->tipo_usuario === 2) 
                        {
                            // Vamos a buscar si el rol que se le esta asignando al usuario
                            // ya lo tiene asignado en la tabla de asignacion_roles;
                            // obtenemos el id de la persona ala que corresponde el nuevo usuario
                            $id_persona_nuevo_usuario = $usuario->persona_id;
                            // Obtenemos el id del rol que se le desea asignar al nuevo usuario
                            $id_rol_personal_nuevo_usuario = $personal_rol->id_rol;
                            $existeRolAsignado = PersonalRolSeleccionado::buscarPorMultiples(
                                [
                                    'id_rol', 
                                    'id_personal'
                                ],
                                [
                                    $id_rol_personal_nuevo_usuario,
                                    $id_persona_nuevo_usuario
                                ]
                            );
                            // Si no hay un registro con el id del personal y el rol asignado 
                            // creamos uno
                            if (campoVacio($existeRolAsignado))
                            {
                                $personal_rol->id_personal = $usuario->persona_id;
                                //debuguear($personal_rol);
                                $resultado = $personal_rol->guardar();
                            }
                        }

                        // Si el usuario no es de tipo 2
                        //asignamos el rol 1 correspondiente a alumno
                        $passwordBeforeHash = $usuario->password;
                        // Hashear el password
                        $usuario->hashPassword();
                        //  Crear un nuevo usuario
                        //debuguear($usuario);
                        $resultado = $usuario->guardar();
                        //$resultado_asignacion_rol = 
                        if ($resultado)
                        {
                            // Crear el objeto de hoja de cálculo
                            $spreadsheet = new Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();
                            $rolInt = (int)$personal_rol->id_rol;
                            //in_array($currentUrl, $this->protectedRolesRoutes[$rolNombre] ?? [])
                            switch ($rolInt) {
                                case 0: $rol = 'Administrador';  break;
                                case 1: $rol = 'Alumno';  break;
                                case 2: $rol = 'Jefe_de_Carrera';  break;
                                case 3: $rol = 'Coordinador_de_Actividades_Extraescolares';  break;
                                case 4: $rol = 'Instructor_de_Actividades_Extraescolares';  break;
                                case 5: $rol = 'Coordinador_de_Créditos_Complementarios';  break;
                                case 6: $rol = 'Supervisor_de_Créditos_Complementarios';  break;
                                case 7: $rol = 'Coordinador_de_Residencias_Profesionales';  break;
                                case 8: $rol = 'Asesor_interno_de_Residencia_Profesional';  break;
                                case 9: $rol = 'Asesor_externo_de_Residencia_Profesional';  break;
                                case 10: $rol = 'Coordinador_de_Lenguas_Extranjeras';  break;
                                case 11: $rol = 'Docente_de_Lenguas_Extranjeras';  break;
                                case 12: $rol = 'Responsable_de_Caja';  break;
                                case 13: $rol = 'Operador_de_Caja';  break;
                                case 14: $rol = 'Coordinador_de_titulaciones';  break;
                                default: $rol = 'unknown'; break;
                            }                            
                            // Encabezados definidos manualmente
                            //$sheet->setCellValue('A1', 'Nombre completo');
                            $sheet->setCellValue('B1', 'Correo electrónico');
                            $sheet->setCellValue('C1', 'Rol del usuario');
                            $sheet->setCellValue('D1', 'Matrícula');
                            $sheet->setCellValue('E1', 'Fecha de registro');                           
                            $sheet->setCellValue('F1', 'Contraseña');
                            
                             // Escribir los datos del usuario
                            //$sheet->setCellValue('A2', $usuario->nombre);
                            $sheet->setCellValue('B2', $usuario->email);
                            $sheet->setCellValue('C2', $rol);
                            $sheet->setCellValue('D2', $usuario->persona_id);
                            $sheet->setCellValue('E2',  date('Y-m-d H:i:s'));
                            $sheet->setCellValue('F2', $passwordBeforeHash);

                            // Guardar archivo
                            $nombreArchivo = 'usuario_'. $rol . $usuario->persona_id . 'Date'. date('Y-m-d') . '.xlsx';
                            $rutaArchivo = CARPETA_EXPORTS . '/' . $nombreArchivo;

                            $writer = new Xlsx($spreadsheet);
                            $writer->save($rutaArchivo);

                            echo "<script>window.open('/archivos-usuarios-descargar?archivo=" . urlencode($nombreArchivo) . "', '_blank');</script>";

                            Usuario::setAlerta('exito', 'Usuario creado correctamente');
                            $alertas = Usuario::getAlertas();
                        }
                    }
            }

        }
        // Render a la vista
        $router->render('usuario/crear', [
            'titulo_pagina' => 'Usuario',
            'sidebar_nav' => 'Usuarios', 
            'usuario' => $usuario,
            'roles' => $roles,
            'alertas' => $alertas
        ]);        
    }

    public static function eliminar()
    {     
        isAuth();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //Validar id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if(!campoVacio($id))
            {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo))
                {
                    $usuario = Usuario::find($id);
                    $resultado = $usuario->eliminar();
                    if (!campoVacio($resultado))
                    {
                        $tabla = 'usuarios';
                        $id_registro = $usuario->id;
                        if ($id_registro !== null)
                        {
                        $_SESSION['mensaje_exito'] = 'El usuario fue eliminado correctamente.';
                        $evento = new BitacoraEventos;
                        $evento->eventos(3, $id_registro, $tabla);
                        header("Location: /usuarios");
                        exit;
                        }
                    } else 
                        {
                        $_SESSION['mensaje_error'] = 'No fue posible eliminar el usuario, probablemente este siendo utilizado en otros registros';
                        header("Location: /usuarios");
                        exit;                               
                        }                    
                }
            }
        }
    }

    public static function cambiar_password(Router $router)
    {
        isAuth();
        $alertas= [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $usuario =  Usuario::find($_SESSION['id_cambiar_password']);
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
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'titulo_pagina' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }     
}