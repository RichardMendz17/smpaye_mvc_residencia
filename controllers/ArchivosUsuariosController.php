<?php

namespace Controllers;

use MVC\Router;
use Model\Aula;
use Model\BitacoraEventos;
use Classes\Paginacion;

class ArchivosUsuariosController {

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
            header('Location: /archivosUsuarios?page=1');
        }
        // Indicamos cuantos registros queremos por pagina
        $registros_por_pagina = 6;

        // Ruta a la carpeta de archivos exportados
        $ruta = CARPETA_EXPORTS;
        $archivos = [];

        if (is_dir($ruta)) {
            $archivos = array_diff(scandir($ruta), ['.', '..']);
            // Ordenamos por fecha de modificación descendente
            usort($archivos, function($a, $b) use ($ruta) {
                return filemtime("$ruta/$b") - filemtime("$ruta/$a");
            });
        }

        // Total de archivos
        $total = count($archivos);

        // Crear el objeto de paginación
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);

        // Cortar el array para mostrar solo los elementos de la página actual
        $inicio = $paginacion->offset();
        $archivos_usuarios = array_slice($archivos, $inicio, $registros_por_pagina);

        $alertas = Aula::getAlertas();        
        $router->render('archivosUsuarios/index', [
            'titulo_pagina' => 'Archivos de Usuarios',
            'sidebar_nav' => 'Archivos Usuarios',            
            'archivos_usuarios'=>$archivos_usuarios,
            'alertas'=>$alertas,
            'paginacion' => $paginacion->paginacion()            
            
        ]);
    }

    public static function descargar(Router $router)
    {
        isAuth();
            $archivo = $_GET['archivo'] ?? '';

            // Validar nombre seguro (nada de rutas raras)
            if (!preg_match('/^[\w\-]+\.xlsx$/', $archivo)) {
                http_response_code(400);
                echo 'Nombre de archivo no válido.';
                return;
            }

            $ruta = CARPETA_EXPORTS . '/' . $archivo;

            if (!file_exists($ruta)) {
                http_response_code(404);
                echo 'Archivo no encontrado.';
                return;
            }

            // Forzar la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta));
            readfile($ruta);
            exit;
    }



    public static function eliminar()
    {
        // Asegúrate de que solo admin pueda hacer esto (si tienes función)
        if (!es_admin()) {
            header('Location: /archivos-usuarios');
            exit;
        }

        // Validar archivo enviado
        $archivo = $_POST['archivo'] ?? '';

        // Evitar rutas maliciosas
        if (!preg_match('/^[\w\-]+\.xlsx$/', $archivo)) {
            echo 'Nombre de archivo no válido.';
            return;
        }

        $ruta = CARPETA_EXPORTS . '/' . $archivo;

        // Eliminar si existe
        if (file_exists($ruta)) {
            unlink($ruta); // 🔥 Elimina el archivo
            // Opcional: redirigir con mensaje
                $_SESSION['mensaje_exito'] = 'El archivo fue eliminado correctamente.';
                header("Location: /archivos-usuarios");
        } else {
                $_SESSION['mensaje_error'] = 'No fue posible eliminar el archivo';
                header("Location: /archivos-usuarios");
                exit;
        }
    }


    public static function buscar(Router $router)
    {
        $alertas = [];
        isAuth();
        //Obtener las columnas para elegir por cual buscar
        $columnasDB = ['Admin', 'Student', 'Career_manager', 'Teacher']; // Simula las columnas
        $archivos_usuarios =  NULL;
        $columna_Seleccionada = null;
        $registro =  NULL;

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $columna_Seleccionada = $_POST['columna'] ?? '';
            $registro = $_POST['dato'] ?? '';

            if ($columna_Seleccionada == '' || $registro ==  '')
            {
                $_SESSION['mensaje_error'] = 'Llene los el formulario complementamente';
                header("Location: /archivos-usuarios-buscar");
                exit;            
            } 
                else 
                {
            $archivos_en_directorio = scandir(CARPETA_EXPORTS);
            foreach ($archivos_en_directorio as $archivo) 
            {
                if (pathinfo($archivo, PATHINFO_EXTENSION) === 'xlsx')
                {
                    if (str_contains($archivo, $columna_Seleccionada) && str_contains($archivo, $registro))
                    {
                        $archivos_usuarios[] = $archivo;
                    }
                }
            }

            if (empty($archivos_usuarios))
            {
                $_SESSION['mensaje_error'] = 'Registro no encontrado';
            } else
                {
                $_SESSION['mensaje_exito'] = 'Registro encontrado';
                }
            }
        }

        $alertas = Aula::getAlertas();
        $router->render('/archivosUsuarios/buscar',[
            'titulo_pagina' => 'Archivos de Usuarios',
            'sidebar_nav' => 'Archivos Usuarios',            
            'archivos_usuarios'=>$archivos_usuarios,
            'alertas'=>$alertas,
            'columnasDB' =>$columnasDB,
            'columna_Seleccionada' => $columna_Seleccionada,
              
        ]);
    }

    
}