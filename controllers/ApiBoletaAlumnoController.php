<?php

    namespace Controllers;
    use PhpOffice\PhpWord\TemplateProcessor;

    use Model\Cursos;
    use Model\AlumnoEnCurso;
    use Model\BoletaAlumno;
    use Model\CursosDetalles;

class ApiBoletaAlumnoController 
{
    // Funcion reutilizable para obtener los datos que van a ir en la boleta
    private static function obtenerDatosBoleta($idCurso, $alumnoId)
    {
        $cursoId = Cursos::where('url', $idCurso);
        if (!$cursoId) return null;

        // Creamos la consulta base para obtener los datos del curso
        $query = "SELECT cursos.id, cursos.url AS curso_url,
        CONCAT(docentes.nombre_Docente, ' ',docentes.apellido_Paterno, ' ', docentes.apellido_Materno) AS nombre_docente,
        niveles.nombre_Nivel as nombre_Nivel,
        CONCAT (periodos.meses_Periodo, ' ', periodos.year) AS periodo,
        aulas.nombre_Aula as nombre_Aula ";
        $query .= " FROM cursos LEFT OUTER JOIN docentes ";
        $query .= " ON cursos.docente_id = docentes.id ";
        $query .= " LEFT OUTER JOIN niveles ";
        $query .= " ON cursos.nivel_id = niveles.id ";
        $query .= " LEFT OUTER JOIN periodos ";
        $query .= " ON cursos.periodo_id = periodos.id ";
        $query .= " LEFT OUTER JOIN aulas ";
        $query .= " ON cursos.aula_id = aulas.id ";            
        $query .= " WHERE cursos.url = '{$idCurso}'";            

        $curso = CursosDetalles::obtenerUnico($query);

        // Datos del alumno en ese curso
        $alumno = AlumnoEnCurso::getAlumnoConDatos($alumnoId, $curso->id);
        if (campoVacio($alumno->calificacion)) return null;

        // Crear boleta
        $boleta = new BoletaAlumno();
        $boleta->nombre_Docente = $curso->nombre_docente;
        $boleta->nivel_Curso = $curso->nombre_Nivel;
        $boleta->Periodo = $curso->periodo;
        $boleta->nombre_Alumno = $alumno->alumno_Nombre;
        $boleta->calificacion_Alumno = $alumno->calificacion;

        return $boleta;
    }


    public static function index()
    {
        // En esta funcion obtenemos todos los datos del curso en el que se encuentre el alumno con su respectiva calificacion, en caso de que 
        // ya tenga asignada una
        isAuth();        
        $cursoId = $_GET['id_curso'] ?? null;
        $cursoId = s($cursoId);

        $boletaAlumno = self::obtenerDatosBoleta($cursoId, $_SESSION['persona_id']);

        echo json_encode([
                'boletaAlumno' => $boletaAlumno,
                'mensaje' => $boletaAlumno ? 'Boleta Disponible' : 'Boleta no disponible',
                'tipo' => $boletaAlumno ? 'exito' : 'error'
            ]);
    }

    public static function generarBoleta()
    {
        isAuth();
        $cursoId = $_GET['id_curso'] ?? null;
        $cursoId = s($cursoId);

        $boletaAlumno = self::obtenerDatosBoleta($cursoId, $_SESSION['persona_id']);
        if (!$boletaAlumno) {
            echo json_encode([
                'mensaje' => 'No se puede generar la boleta. Datos incompletos.',
                'tipo' => 'error'
            ]);
            return;
        }

        // Aquí sí generas el archivo Word o PDF usando PhpWord, etc.
        $rutaPlantilla = dirname(__DIR__) . '/storage/Plantilla_Boleta/Plantilla_Boleta.docx';
        $template = new TemplateProcessor($rutaPlantilla);
        $template->setValue('nombre_Alumno', $boletaAlumno->nombre_Alumno);
        $template->setValue('nombre_Docente', $boletaAlumno->nombre_Docente);
        $template->setValue('nivel_Curso', $boletaAlumno->nivel_Curso);
        $template->setValue('Periodo', $boletaAlumno->Periodo);
        $template->setValue('calificacion', $boletaAlumno->calificacion_Alumno);

        $tempFile = tempnam(sys_get_temp_dir(), 'boleta');
        $template->saveAs($tempFile);

        header("Content-Disposition: attachment; filename=boleta.docx");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        readfile($tempFile);
        unlink($tempFile);
    }

}
?>