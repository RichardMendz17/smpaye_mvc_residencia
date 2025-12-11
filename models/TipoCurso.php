<?php 

    namespace Model;
    class TipoCurso extends ActiveRecord
    {

    // Base de datos
    protected static $tabla = 'tipos_curso';
    protected static  $columnasDB = ['id', 'nombre_curso', 'modulo_id'];
    protected static $alertas = [];

    public $id;
    public $nombre_curso;
    public $modulo_id;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre_curso = isset($args['nombre_curso']) ? trim($args['nombre_curso']) : '';
        $this->modulo_id = isset($args['modulo_id']) ? trim($args['modulo_id']) : '';

    }

    public function validar()
    {
        if(!$this->nombre_curso){
            self::$alertas['error'][] = 'El Nombre del Curso es obligatorio';
        }
        if(!$this->modulo_id){
            self::$alertas['error'][] = 'El Modulo del curso es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas()
    {
        return self::$columnasDB;
    }
    
    }
?>