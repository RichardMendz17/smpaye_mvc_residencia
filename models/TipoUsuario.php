<?php 

    namespace Model;
    class TipoUsuario extends ActiveRecord
    {

    // Base de datos
    protected static $tabla = 'tipos_usuario';
    protected static  $columnasDB = ['id', 'nombre', 'descripcion'];
    protected static $alertas = [];

    public $id;
    public $nombre;
    public $descripcion;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre = isset($args['nombre']) ? trim($args['nombre']) : '';
        $this->descripcion = isset($args['descripcion']) ? trim($args['descripcion']) : '';

    }


    public static function obtenerColumnas()
    {
        return self::$columnasDB;
    }
    
    }
?>