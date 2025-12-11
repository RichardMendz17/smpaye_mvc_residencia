<?php 

    namespace Model;
    class Aula extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'aulas';
    protected static  $columnasDB = ['id', 'nombre_Aula'];
    protected static $alertas = [];

    public $id;
    public $nombre_Aula;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre_Aula = isset($args['nombre_Aula']) ? trim($args['nombre_Aula']) : '';

    }

    public function validar(){
        if(!$this->nombre_Aula){
            self::$alertas['error'][] = 'El Nombre del Aula es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerColumnas() {
        return self::$columnasDB;
    }
    }
?>