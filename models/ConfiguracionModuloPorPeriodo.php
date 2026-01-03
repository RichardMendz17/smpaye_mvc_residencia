<?php 

    namespace Model;
    class ConfiguracionModuloPorPeriodo extends ActiveRecord {

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
        $this->maximo_cursos_por_periodo = isset($args['maximo_cursos_por_periodo']) ? trim($args['maximo_cursos_por_periodo']) : '';
        $this->fecha_limite_inscripcion = isset($args['fecha_limite_inscripcion']) ? trim($args['fecha_limite_inscripcion']) : '';

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
        if(!campoVacio($this->maximo_cursos_por_periodo) && $this->maximo_cursos_por_periodo < 1){
            self::$alertas['error'][] = 'La cantidad maxica de cursos por periodo es invalida';
        }
        return self::$alertas;
    }

    public function validarFechaLimiteDeInscripcion()
    {
        if(campoVacio($this->fecha_limite_inscripcion)){
            self::$alertas['error'][] = 'La fecha limite de inscripcion es obligatorio';
        }
        return self::$alertas;
    }
    public static function obtenerColumnas()
    {
        return self::$columnasDB;
    }
    }
?>