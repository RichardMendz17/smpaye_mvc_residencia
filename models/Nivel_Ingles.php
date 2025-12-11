<?php 

    namespace Model;
    class Nivel_Ingles extends ActiveRecord
    {

    // Base de datos
    protected static $tabla = 'niveles_ingles';
    protected static  $columnasDB = ['id', 'nombre_Nivel'];
    protected static $alertas = [];

    public $id;
    public $nombre_Nivel;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre_Nivel = isset($args['nombre_Nivel']) ? trim($args['nombre_Nivel']) : '';

    }

    public function validar()
    {
        if(!$this->nombre_Nivel){
            self::$alertas['error'][] = 'El Nombre del Nivel es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas()
    {
        return self::$columnasDB;
    }
    
    }
?>