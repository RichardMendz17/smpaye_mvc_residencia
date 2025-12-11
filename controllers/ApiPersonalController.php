<?php 
namespace Controllers;

use Model\Curso;
use Model\AlumnoEnCurso;
use Model\AlumnoDetalles;
use Model\AlumnoCursoDetalles;
use Model\Personal;

class ApiPersonalController
{
    public static function index()
    {
        $cursoId = $_GET['id'];
        if(!$cursoId) header('Location /dashboard');
        $curso = Curso::where('url',$cursoId);
        $alumnos = AlumnoEnCurso::getAlumnosConDatos('curso_detalle_id', $curso->id);
        echo json_encode(['alumnos' => $alumnos]);

    }


    public static function buscar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $numeroControl = $_GET['numero_control'] ?? '';

            // Validar que tenga al menos 3 caracteres
            if(strlen($numeroControl) <= 3) {
                echo json_encode(['personal_Institucional_Detalles' => []]);
                return;
            }

            $query = "SELECT personal.id, personal.nombre, personal.apellido_Paterno, personal.apellido_Materno ";
            $query .= "FROM personal ";
            $query .= "WHERE personal.id LIKE '%{$numeroControl}%' ";
            $query .= " ORDER BY personal.id DESC ";
            $query .= "LIMIT 5 ";

            $persona = Personal::SQL($query);

            echo json_encode(['personal_Detalles' => $persona]);
        }
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Cursos::where('url', $_POST['cursoUrl']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            $alumnosId = json_decode($_POST['id'], true);

            $resultado = AlumnoCursoDetalles::elminarAlumnoCurso('curso_detalle_id', $cursoDetalleId, 'alumno_id', $alumnosId); // Identificamos el curso

            echo json_encode([
                'resultado' => $resultado,
                'mensaje' => $resultado ? 'Alumno eliminado correctamente' : 'No se pudo eliminar el alumno',
                'tipo' => $resultado ? 'exito' : 'error'
            ]);
        }
    }
    public static function agregar_alumnos()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Cursos::where('url', $_POST['curso_id']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $alumnosIds = json_decode($_POST['alumnos_ids'], true);         
            // Obtener el curso_detalle_id
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            foreach ($alumnosIds as $alumnoIdString)
            {
           // Conversión a int con validación
            $alumnoId = filter_var($alumnoIdString, FILTER_VALIDATE_INT);

            // Crear nuevo registro
            $inscripcion = new AlumnoCursoDetalles([
                'curso_detalle_id' => $cursoDetalleId,
                'alumno_id' => $alumnoId,
                'referencia' => null, // Enviamos NULL explícitamente
                'estatus' => 'inscrito' // valor por defecto
            ]);

            $resultado = $inscripcion->guardar();
            }
            if ($resultado) {
                $resultado = [
                'resultado' => $resultado,
                'mensaje' => 'Alumnos Agregados Correctamente',
                'tipo' => 'exito',
            ];
            echo json_encode($resultado);
            }  
        }
    }    
    public static function asignar_calificacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Cursos::where('url', $_POST['cursoUrl']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            $alumnosId = json_decode($_POST['id'], true);
            $calificacion_Asignada = json_decode($_POST['calificacion'], true);

            // Validación previa recomendada, tambien se implemento en js pero por seguridad se vuelve a aplicar
            if (!is_numeric($calificacion_Asignada)) 
            {
                echo json_encode
                ([
                'resultado' => 'resultado',
                'mensaje' => 'No se pudo asignar la calificación  del alumno',
                'tipo' => 'error'
                ]);
                return;
            } elseif ($calificacion_Asignada >= 70) {
                $estatus = 'aprobado';
            } elseif ($calificacion_Asignada >= 1) {
                $estatus = 'reprobado';
            } else {
                $estatus = 'retirado';
            }
            $resultado = AlumnoCursoDetalles::asignar_alumno_valores
            (
                'curso_detalle_id', $cursoDetalleId,
                'alumno_id', $alumnosId,
                [
                'calificacion' => $calificacion_Asignada,
                'estatus' => $estatus
                ]
            ); // Identificamos el curso

            echo json_encode([
                'resultado' => $resultado,
                'calificacion' => $calificacion_Asignada,
                'estatus' => $estatus, // Calculado en backend                
                'mensaje' => $resultado ? 'Calificacion asignada correctamente' : 'No se pudo asignar la calificación  del alumno',
                'tipo' => $resultado ? 'exito' : 'error'
            ]);
        }
    }
    public static function asignar_referencia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Cursos::where('url', $_POST['cursoUrl']); // Identificamos el curso
            // Decodificar los IDs de alumnos (asumiendo que vienen como JSON)
            if (!$curso /*|| $proyecto->propietarioId !== $_SESSION['id']*/)
            {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            $cursoDetalleId = (int)$curso->id; // Aseguramos que sea integer
            $alumnosId = json_decode($_POST['id'], true);
            $referencia_Asignada = $_POST['referencia'];

            $referencia_Asignada_String = (string)$referencia_Asignada; // Aseguramos que sea integer

            // Validación previa recomendada, tambien se implemento en js pero por seguridad se vuelve a aplicar
            if (!is_numeric($referencia_Asignada)) 
            {
                echo json_encode
                ([
                'resultado' => 'resultado',
                'mensaje' => 'No se pudo asignar la referencia del alumno',
                'tipo' => 'error'
                ]);
                return;
            } elseif (strlen($referencia_Asignada_String) !== 20 || !ctype_digit($referencia_Asignada_String))
                    {
                        return;
                    }
            $resultado = AlumnoCursoDetalles::asignar_alumno_valores
            (
                'curso_detalle_id', $cursoDetalleId,
                'alumno_id', $alumnosId,
                [
                'referencia' => $referencia_Asignada
                ]
            ); // Identificamos el curso
            echo json_encode([
                'resultado' => $resultado,
                'referencia' => $referencia_Asignada,
                'mensaje' => $resultado ? 'Referencia asignada correctamente' : 'No se pudo asignar la referencia  del alumno',
                'tipo' => $resultado ? 'exito' : 'error'
            ]);
        }
    }        
}

?>