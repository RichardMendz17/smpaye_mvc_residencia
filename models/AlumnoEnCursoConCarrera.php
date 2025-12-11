<?php 
    namespace Model;
    class AlumnoEnCursoConCarrera extends ActiveRecord{
       
    // Base de datos
    protected static $tabla = 'alumno_curso_detalles';
    protected static $columnasDB = ['alumno_id','referencia', 'fecha_inscripcion', 'calificacion', 'estatus', 'alumno_Nombre', 'nombre_Carrera'];

    public $alumno_id;
    public $referencia;
    public $fecha_inscripcion;
    public $calificacion;
    public $estatus;
    public $alumno_Nombre;
    public $nombre_Carrera;

    public function __construct($args = [])
    {
        $this->alumno_id = isset($args['alumno_id']) ? trim($args['alumno_id']) : '';
        $this->referencia = isset($args['referencia']) ? trim($args['referencia']) : '';
        $this->fecha_inscripcion = isset($args['fecha_inscripcion']) ? trim($args['fecha_inscripcion']) : '';
        $this->calificacion = isset($args['calificacion']) ? trim($args['calificacion']) : null;    
        $this->estatus = $args['estatus'] ?? 'inscrito';
        $this->alumno_Nombre = isset($args['alumno_Nombre']) ? trim($args['alumno_Nombre']) : null;    
        $this->nombre_Carrera = isset($args['nombre_Carrera']) ? trim($args['nombre_Carrera']) : null;    
    }

    public static function getAlumnosConDatos($columna, $valor)
    {
        $query = "SELECT 
                    alumno_curso_detalles.alumno_id,
                    alumno_curso_detalles.referencia,
                    alumno_curso_detalles.fecha_inscripcion,
                    alumno_curso_detalles.calificacion,
                    alumno_curso_detalles.estatus,
                    CONCAT(alumnos.nombre_Alumno, ' ', alumnos.apellido_Paterno, ' ', alumnos.apellido_Materno) as alumno_Nombre,
                    carreras.nombre_Carrera as nombre_Carrera
                FROM " . static::$tabla . " alumno_curso_detalles
                JOIN alumnos ON alumno_curso_detalles.alumno_id = alumnos.id
                LEFT JOIN carreras ON alumnos.id_Carrera = carreras.id
                WHERE alumno_curso_detalles.{$columna} = '{$valor}'";

        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function getAlumnoConDatos($alumno_id, $curso_detalle_id)
    {
        $query = "SELECT 
                    alumno_curso_detalles.alumno_id,
                    alumno_curso_detalles.referencia,
                    alumno_curso_detalles.fecha_inscripcion,
                    alumno_curso_detalles.calificacion,
                    alumno_curso_detalles.estatus,
                    CONCAT(alumnos.nombre_Alumno, ' ', alumnos.apellido_Paterno, ' ', alumnos.apellido_Materno) as alumno_Nombre,
                    carreras.nombre_Carrera as nombre_Carrera
                FROM " . static::$tabla . " alumno_curso_detalles
                JOIN alumnos ON alumno_curso_detalles.alumno_id = alumnos.id
                LEFT JOIN carreras ON alumnos.id_Carrera = carreras.id
                WHERE alumno_curso_detalles.alumno_id = '{$alumno_id}' AND
                alumno_curso_detalles.curso_detalle_id = '{$curso_detalle_id}'";
        $resultado = self::obtenerUnico($query);
        return $resultado;
    }
    public static function getAlumnoConDatosAndCompañeros($columna, $valor, $alumnoSesion)
    {
        // El ID del alumno que inició sesión
        $query = "SELECT 
                    alumno_curso_detalles.alumno_id,
                    alumno_curso_detalles.referencia,
                    alumno_curso_detalles.fecha_inscripcion,
                    -- Mostrar calificación solo si es el alumno en sesión
                    CASE 
                        WHEN alumno_curso_detalles.alumno_id = {$alumnoSesion} THEN alumno_curso_detalles.calificacion
                        ELSE 'No disponible'
                    END AS calificacion,
                    -- Igual para estatus
                    CASE 
                        WHEN alumno_curso_detalles.alumno_id = {$alumnoSesion} THEN alumno_curso_detalles.estatus
                        ELSE 'No disponible'
                    END AS estatus,
                    CONCAT(alumnos.nombre_Alumno, ' ', alumnos.apellido_Paterno, ' ', alumnos.apellido_Materno) AS alumno_Nombre,
                    carreras.nombre_Carrera AS nombre_Carrera
                FROM " . static::$tabla . " alumno_curso_detalles
                JOIN alumnos ON alumno_curso_detalles.alumno_id = alumnos.id
                LEFT JOIN carreras ON alumnos.id_Carrera = carreras.id
                WHERE alumno_curso_detalles.{$columna} = '{$valor}'";
                //($query);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

}
?>