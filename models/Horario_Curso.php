<?php 

    namespace Model;
    class Horario_Curso extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'horarios_clase';
    protected static  $columnasDB = ['id', 'clase_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
    protected static $alertas = [];

    public $id;
    public $clase_id;
    public $dia_semana;
    public $hora_inicio;
    public $hora_fin;

        
    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->clase_id = isset($args['clase_id']) ? trim($args['clase_id']) : '';
        $this->dia_semana = isset($args['dia_semana']) ? trim($args['dia_semana']) : '';
        $this->hora_inicio = isset($args['hora_inicio']) ? trim($args['hora_inicio']) : '';
        $this->hora_fin = isset($args['hora_fin']) ? trim($args['hora_fin']) : '';
    } 

    public function validar(){
        if(!$this->clase_id){
            self::$alertas['error'][] = 'La clase asociada es obligatoria.';
        }
        $diasValidos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        if($this->dia_semana && !in_array(strtolower($this->dia_semana), $diasValidos)){
            self::$alertas['error'][] = 'El día ingresado no es válido.';
        }
        if(!$this->hora_inicio){
            self::$alertas['error'][] = 'La hora de inicio del curso correspondiente al día es obligatorio';
        }
        if(!$this->hora_fin){
            self::$alertas['error'][] = 'La hora de fin del curso correspondiente al día es obligatorio';
        }
        if($this->hora_inicio && $this->hora_fin && $this->hora_inicio >= $this->hora_fin){
            self::$alertas['error'][] = 'La hora de inicio debe ser menor que la hora de fin.';
        }
        return self::$alertas;
    }


    }
?>