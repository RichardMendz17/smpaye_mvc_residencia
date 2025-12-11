<?php 

namespace Controllers;

use Model\AlumnoCursoDetalles;
use Model\Curso;
use Model\AlumnoEnCursoConCarrera;
use Model\AlumnoDetalles;

class ApiAlumnosController
{
    public static function index()
    {   
        isAuth();        
        $cursoId = $_GET['id'];

        if(!$cursoId) header('Location /dashboard');

        $curso = Curso::where('url', $cursoId);

       if(es_extracurricular_activities_coordinator() || es_extracurricular_activities_instructor())
        {
        $alumnos = AlumnoEnCursoConCarrera::getAlumnosConDatos('curso_detalle_id', $curso->id);
        }
        if(es_student())
        {
        $alumnos = AlumnoEnCursoConCarrera::getAlumnoConDatosAndCompañeros('curso_detalle_id', $curso->id, $_SESSION['persona_id']);
        }
        echo json_encode(['alumnos' => $alumnos]);
    }


    public static function buscar()
    {
        isAuth();        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $numeroControl = $_GET['numero_control'] ?? '';

            // Validar que tenga al menos 3 caracteres
            if(strlen($numeroControl) <= 3) {
                echo json_encode(['alumnosDetalles' => []]);
                return;
            }
            $query = "SELECT alumnos.id, alumnos.nombre_Alumno, alumnos.apellido_Paterno, alumnos.apellido_Materno, alumnos.comentarios, carreras.nombre_Carrera ";
            $query .= "FROM alumnos ";
            $query .= "LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id ";
            $query .= "WHERE alumnos.id LIKE '%{$numeroControl}%'";
            $query .= "ORDER BY alumnos.id DESC ";
            $query .= "LIMIT 5 ";
            $alumnosDetalles = AlumnoDetalles::SQL($query);
            echo json_encode(['alumnosDetalles' => $alumnosDetalles]);
        }
    }

    public static function eliminar()
    {
        isAuth();        
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Curso::where('url', $_POST['cursoUrl']); 
            // Identificamos el curso
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
        isAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Curso::where('url', $_POST['curso_id']); // Identificamos el curso
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
            if ($resultado)
            {
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
        isAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            // VALIDAR QUE EL PROYECTO EXISTA
            $curso = Curso::where('url', $_POST['cursoUrl']); // Identificamos el curso
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
            $alumnoId = json_decode($_POST['id'], true);
            $calificacion_Asignada = json_decode($_POST['calificacion'], true);
            $alumno = new AlumnoCursoDetalles;
            $alumno = AlumnoCursoDetalles::buscarPorMultiples(
            ['alumno_id', 'curso_detalle_id'],
            [$alumnoId, $cursoDetalleId]
            );

            //verificamos que si el alumno ya tiene una calificacion y un rol diferente de admin quiera reasignar una calificacion no pueda
            if ($_SESSION['rol'] !== 0 && !campoVacio($alumno->calificacion))
            {
                echo json_encode
                ([
                'resultado' => 'resultado',
                'mensaje' => 'No se pudo reasignar la calificación  del alumno',
                'tipo' => 'error'
                ]);
            }
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
                'alumno_id', $alumnoId,
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
            $curso = Curso::where('url', $_POST['cursoUrl']); // Identificamos el curso
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