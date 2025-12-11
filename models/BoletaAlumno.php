<?php 
    namespace Model;

    class BoletaAlumno extends ActiveRecord{
        
        public $nombre_Docente;
        public $nivel_Curso;
        public $Periodo;
        public $nombre_Alumno;
        public $calificacion_Alumno;

        public function __construct($args = [])
        {
            $this->nombre_Docente = isset($args['numero_control']) ? trim($args['numero_control']) : '';
            $this->nivel_Curso = isset($args['nombre_Alumno']) ? trim($args['nombre_Alumno']) : '';
            $this->Periodo = isset($args['curso']) ? trim($args['curso']) : '';
            $this->nombre_Alumno = isset($args['nombre_Docente']) ? trim($args['nombre_Docente']) : null;    
            $this->calificacion_Alumno = isset($args['estatus']) ? trim($args['estatus']) : null;    
        }

    }
?>