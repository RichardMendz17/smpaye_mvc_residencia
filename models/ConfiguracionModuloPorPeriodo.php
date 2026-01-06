<?php 

    namespace Model;

use DateTime;

    class ConfiguracionModuloPorPeriodo extends ActiveRecord 
    {

        // Base de datos
        protected static $tabla = 'configuracion_modulo_periodo';
        protected static  $columnasDB = ['id', 'id_modulo', 'id_periodo', 'maximo_cursos_por_periodo', 'fecha_limite_inscripcion'];
        protected static $alertas = [];

        public $id;
        public $id_modulo;
        public $id_periodo;
        public $maximo_cursos_por_periodo;
        public $fecha_limite_inscripcion;

            
        public function __construct($args = [])
        {
            $this->id = $args['id'] ?? NULL;
            $this->id_modulo = isset($args['id_modulo']) ? trim($args['id_modulo']) : '';
            $this->id_periodo = isset($args['id_periodo']) ? trim($args['id_periodo']) : '';
            $this->maximo_cursos_por_periodo = (isset($args['maximo_cursos_por_periodo']) && trim($args['maximo_cursos_por_periodo']) !== '')
            ? trim($args['maximo_cursos_por_periodo']) : null;
            $this->fecha_limite_inscripcion = (isset($args['fecha_limite_inscripcion']) && trim($args['fecha_limite_inscripcion']) !== '')
            ? trim($args['fecha_limite_inscripcion']) : null;
        }

        public function validar(){
            if(campoVacio($this->id_modulo)){
                self::$alertas['error'][] = 'El id del modulo es obligatorio';
            }
            if(campoVacio($this->id_periodo)){
                self::$alertas['error'][] = 'El id del periodo es obligatorio';
            }
            return self::$alertas;
        }
        
        public function validarMaximoCursosPorPeriodo()
        {
            if(campoVacio($this->maximo_cursos_por_periodo)){
                self::$alertas['error'][] = 'El maximo de cursos por periodo es obligatorio';
            }
            // Validar que sea int
            if (!filter_var($this->maximo_cursos_por_periodo, FILTER_VALIDATE_INT)) {
                self::$alertas['error'][] = 'El valor de limite de cursos debe ser un número entero válido';
            } else {
                // Validar rango
                if ($this->maximo_cursos_por_periodo < 1) {
                    self::$alertas['error'][] = 'La cantidad máxima de cursos por periodo es inválida';
                }
            }
            return self::$alertas;
        }

        public function validarFechaLimiteDeInscripcion()
        {
            if( campoVacio($this->fecha_limite_inscripcion) || !DateTime::createFromFormat('Y-m-d', $this->fecha_limite_inscripcion)) 
            {   // Ejemplo de como aplicar una doble validacion con una alerta adecuada
                self::$alertas['error'][] = 'La fecha límite de inscripción es obligatoria y debe ser válida';
            }
                return self::$alertas;
        }

        public function validar_Fecha_Limite_De_Inscripcion_Con_Fecha_de_Inicio_y_Final_De_Periodo($periodo_seleccionado)
        {
            // extraemos los valores de periodo ya que este es un objeto
            $fecha_limite_inscripcion_seleccionado = $this->fecha_limite_inscripcion;
            $fecha_inicio_periodo = $periodo_seleccionado->fecha_inicio;
            $fecha_final_periodo = $periodo_seleccionado->fecha_fin;
            if($fecha_limite_inscripcion_seleccionado < $fecha_inicio_periodo || $fecha_limite_inscripcion_seleccionado > $fecha_final_periodo)
            {
                self::$alertas['error'][] = 'La fecha límite de inscripcion debe estar dentro del periodo';
            }
                return self::$alertas;
        }

        public static function obtenerColumnas()
        {
            return self::$columnasDB;
        }
    }
