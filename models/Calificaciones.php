<?php 
    namespace Model;

    class Calificaciones extends ActiveRecord{
       
    // Base de datos
    protected static $tabla = 'alumnos';
    protected static $columnasDB = ['numero_control','nombre_Alumno', 'curso', 'nombre_Docente', 'periodo', 'calificacion', 'estatus', 'fecha_inscripcion'];



    public $numero_control;
    public $nombre_Alumno;
    public $curso;
    public $nombre_Docente;
    public $periodo;
    public $calificacion;
    public $estatus;
    public $fecha_inscripcion;


    public function __construct($args = [])
    {
        $this->numero_control = isset($args['numero_control']) ? trim($args['numero_control']) : '';
        $this->nombre_Alumno = isset($args['nombre_Alumno']) ? trim($args['nombre_Alumno']) : '';
        $this->curso = isset($args['curso']) ? trim($args['curso']) : '';
        $this->nombre_Docente = isset($args['nombre_Docente']) ? trim($args['nombre_Docente']) : null;    
        $this->estatus = isset($args['estatus']) ? trim($args['estatus']) : null;    
        $this->fecha_inscripcion = isset($args['fecha_inscripcion']) ? trim($args['fecha_inscripcion']) : null;    
    }

}
?>