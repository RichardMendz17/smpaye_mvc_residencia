<?php 

    namespace Model;
    class Carrera extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'carreras';
    protected static  $columnasDB = ['id', 'nombre_Carrera'];
    protected static $alertas = [];

    public $id;
    public $nombre_Carrera;

        
    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->nombre_Carrera = isset($args['nombre_Carrera']) ? trim($args['nombre_Carrera']) : '';

    }

    public function validar(){
        if(!$this->nombre_Carrera){
            self::$alertas['error'][] = 'El Nombre de la carrera es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas() {
        return self::$columnasDB;
    }
    }
?>