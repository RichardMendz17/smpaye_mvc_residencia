<?php 

namespace Controllers;


use Model\Curso;
use Model\Horario_Curso;

class ApiHorariosClaseController
{
    public static function index()
    {
        $cursoId = $_GET['id'];
        if(!$cursoId) header('Location /dashboard');
        $curso = Curso::where('url',$cursoId);
        $horario = Horario_Curso::belongsTo('clase_id', $curso->id);
        echo json_encode(['horario' => $horario]);
    }


    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {

            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Curso::where('url', $_POST['curso_id']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al guardar los cambios del horario'
                ];
                echo json_encode($respuesta);
                return;
            }
            $eliminarDias = json_decode($_POST['eliminarDias'], true); // Esto ya es un array de arrays asociativos
    
            // Obtener el curso_detalle_id
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            foreach ($eliminarDias as $eliminarDia)
            {
                // Crear nuevo registro
                $eliminarRegistroHorario = new Horario_Curso([
                    'id' => $eliminarDia['id'],
                    'clase_id' => $cursoDetalleId,
                ]);
                $resultado = $eliminarRegistroHorario->eliminar();            
                if ($resultado)
                {
                    $diasEliminados[] = [
                        'id' => $eliminarDia['id']
                    ];
                }
            }
            if ($resultado)
            {
                $resultado = [
                'resultado' => $resultado,
                'mensaje' => $resultado ? 'Días eliminados del horario Correctamente': 'Hubo algún error al intentar eliminar los dias del horario',
                'tipo' => $resultado ? 'exito' : 'error',
                'diasEliminados' => $diasEliminados
            ];
            echo json_encode($resultado);
            }  
        }
    }
    public static function agregar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Curso::where('url', $_POST['curso_id']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al guardar los cambios del horario'
                ];
                echo json_encode($respuesta);
                return;
            }
            // Obtener el curso_detalle_id
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            
            //Obtener el arreglo de los dias y las horas de entrada y salida que se van a crear o actualizar
            $agregarDias = json_decode($_POST['guardarDias'], true);     
    
            // Creamos un arreglo para poder almacenar el id de los registros que se van a guardar
            $DiasGuardados = []; 
            foreach ($agregarDias as $agregarDia)
            {
            // Crear nuevo registro
            $agregarRegistroHorario = new Horario_Curso([
                'id' => $agregarDia['id'] ?? null,
                'clase_id' => $cursoDetalleId,
                'dia_semana' => $agregarDia['dia_semana'],
                'hora_inicio' => $agregarDia['hora_inicio'], // Enviamos NULL explícitamente
                'hora_fin' => $agregarDia['hora_fin'] // valor por defecto
            ]);
            $resultado = $agregarRegistroHorario->guardar();

            if (isset($resultado['id']))
            {
                $DiasGuardados[] = [
                    'dia_semana' => $agregarDia['dia_semana'],
                    'clase_id' => $cursoDetalleId,
                    'id' => $resultado['id']
                ];
            }
            }
            
            $resultado = ([
                'tipo' => count($DiasGuardados) > 0 ? 'exito' : 'error',
                'mensaje' => count($DiasGuardados) > 0 
                    ? 'Cambios del horario agregados correctamente' 
                    : 'No se realizaron cambios en el horario',
                'DiasGuardados' => $DiasGuardados
            ]);
            echo json_encode($resultado);
            }
    }

}
?>