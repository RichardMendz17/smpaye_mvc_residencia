<?php 

    namespace Model;
    class Rol extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'roles';
    protected static  $columnasDB = ['id', 'rol'];
    protected static $alertas = [];

    public $id;
    public $rol;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->rol = isset($args['rol']) ? trim($args['rol']) : '';

    }

    public function validar(){
        if(!$this->rol){
            self::$alertas['error'][] = 'El Rol es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas() 
    {
        return self::$columnasDB;
    }
}
?>