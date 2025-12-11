<?php 

    namespace Model;
    class TipoCursoDetalles extends ActiveRecord
    {

    // Base de datos
    protected static $tabla = 'tipos_curso';
    protected static  $columnasDB = ['id', 'nombre_curso', 'nombre_modulo'];
    protected static $alertas = [];

    public $id;
    public $nombre_curso;
    public $nombre_modulo;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre_curso = isset($args['nombre_curso']) ? trim($args['nombre_curso']) : '';
        $this->nombre_modulo = isset($args['nombre_modulo']) ? trim($args['nombre_modulo']) : '';

    }

    public static function obtenerColumnas()
    {
        return self::$columnasDB;
    }
    
    }
?>