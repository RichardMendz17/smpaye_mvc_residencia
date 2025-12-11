<?php 
    namespace Model;

    class AlumnoCursoDetalles extends ActiveRecord{
       
    // Base de datos
    protected static $tabla = 'alumno_curso_detalles';
    protected static $columnasDB = ['id','curso_detalle_id', 'alumno_id', 'referencia', 'fecha_inscripcion', 'calificacion', 'estatus'];

    public $id;
    public $alumno_id;
    public $curso_detalle_id;
    public $referencia;
    public $fecha_inscripcion;
    public $calificacion;
    public $estatus;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        // Campos que deben ser integers
        $this->curso_detalle_id = isset($args['curso_detalle_id']) ? (int)$args['curso_detalle_id'] : 0;
        $this->alumno_id = isset($args['alumno_id']) ? (int)$args['alumno_id'] : 0;
        // Manejo explícito de NULL para referencia
        $this->referencia = $args['referencia'] ?? null;        // Campos string
        $this->fecha_inscripcion = isset($args['fecha_inscripcion']) ? trim($args['fecha_inscripcion']) : date('Y-m-d H:i:s');
        $this->calificacion = isset($args['calificacion']) ? trim($args['calificacion']) : null;
        $this->estatus = $args['estatus'] ?? 'inscrito';
    }
    public static function getAlumnosConDatos($columna, $valor) {
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
    public static function elminarAlumnoCurso($columna1, $valor1, $columna2, $valor2) 
    {
        $query = "DELETE FROM " . static::$tabla;
        $query .= " WHERE {$columna1} = {$valor1} ";
        $query .= " AND {$columna2} = {$valor2} ";
        $resultado = self::unicoSQL($query);
        return $resultado;    
    }
    
    public static function asignar_alumno_valores($columna1, $valor1, $columna2, $valor2, $sets = [])
    {
        // Validación básica
        if (empty($sets)) {
            throw new Exception("Debes proporcionar al menos un SET para actualizar");
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        
        // Construye los SETs dinámicamente
        $updates = [];
        foreach ($sets as $columna => $valor) {
            $updates[] = "{$columna} = '{$valor}'";  // Nota: Escapa los valores en producción
        }
        $query .= implode(', ', $updates);
        
        // WHERE conditions
        $query .= " WHERE {$columna1} = '{$valor1}' AND {$columna2} = '{$valor2}' LIMIT 1";

        $resultado = self::unicoSQL($query);
        return $resultado;
    }
}
?>