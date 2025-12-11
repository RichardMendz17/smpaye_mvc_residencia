<?php 

    namespace Model;
    class PersonalRolDetalles extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'asignacion_roles';
    protected static  $columnasDB = ['id', 'id_personal', 'nombre_personal',  'id_rol', 'nombre_rol'];
    protected static $alertas = [];

    public $id;
    public $id_personal;
    public $nombre_personal;
    public $id_rol;
    public $nombre_rol;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->id_personal = isset($args['id_personal']) ? trim($args['id_personal']) : '';
        $this->nombre_personal = isset($args['nombre_personal']) ? trim($args['nombre_personal']) : '';
        $this->id_rol = isset($args['id_rol']) ? trim($args['id_rol']) : '';
        $this->nombre_rol = isset($args['nombre_rol']) ? trim($args['nombre_rol']) : '';

    }

    public static function obtenerColumnas() {
        return self::$columnasDB;
    }
    }
?>