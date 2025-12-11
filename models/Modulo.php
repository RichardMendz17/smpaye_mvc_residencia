<?php 

    namespace Model;
    class Modulo extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'modulos';
    protected static  $columnasDB = ['id', 'nombre_modulo'];
    protected static $alertas = [];

    public $id;
    public $nombre_modulo;

        
    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->nombre_modulo = isset($args['nombre_modulo']) ? trim($args['nombre_modulo']) : '';

    }

    public function validar(){
        if(campoVacio($this->nombre_modulo)){
            self::$alertas['error'][] = 'El  del modulo es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas() {
        return self::$columnasDB;
    }
    }
?>