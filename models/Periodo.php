<?php 

    namespace Model;
    class Periodo extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'periodos';
    protected static  $columnasDB = ['id', 'meses_Periodo', 'year', 'estado', 'fecha_inicio', 'fecha_fin'];

    public $id;
    public $meses_Periodo;
    public $year;
    public $estado;
    public $fecha_inicio;
    public $fecha_fin;

        
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->meses_Periodo = isset($args['meses_Periodo']) ? trim($args['meses_Periodo']) : '';
        $this->year = isset($args['year']) ? trim($args['year']) : '';
        $this->estado = isset($args['estado']) ? trim($args['estado']) : '';
        $this->fecha_inicio = isset($args['fecha_inicio']) ? trim($args['fecha_inicio']) : '';
        $this->fecha_fin = isset($args['fecha_fin']) ? trim($args['fecha_fin']) : '';

    } 

    public function validar()
    {
        if(!$this->meses_Periodo){
            self::$alertas['error'][] = 'El Periodo es obligatorio';
        }
        if(!$this->year){
            self::$alertas['error'][] = 'El Año del periodo es obligatorio';
        }
        if(!$this->estado){
            self::$alertas['error'][] = 'El Estado del periodo es obligatorio';
        }    
        if(!$this->fecha_inicio){
            self::$alertas['error'][] = 'La fecha de inicio del periodo es obligatorio';
        }    
        if(!$this->fecha_fin){
            self::$alertas['error'][] = 'La fecha de fin del periodo es obligatorio';
        }
        if ($this->fecha_inicio && $this->fecha_fin)
        {
            if ($this->fecha_inicio > $this->fecha_fin) 
            {
                self::$alertas['error'][] = 'La fecha de inicio no puede ser despues que la fecha final';
            }
        }
        return self::$alertas;
    }

    public static function obtenerColumnas() {
        return self::$columnasDB;
    }

    // Busca un registro por sus columnas
    public static function verificar_meses_Periodo($columna, $valor, $columna2, $valor2)
    {
        $query = "SELECT * FROM " . self::$tabla  ." WHERE {$columna} = '{$valor}' and {$columna2} = '{$valor2}'";
        $resultado = self::consultarSQL($query);        
        if (empty($resultado)) {
            return true; 
        }
        return false;
    }

    public function actualizarRegistro()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
        // Construir la consulta completa
        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= "') ";
        $query .= " ON DUPLICATE KEY UPDATE ";
        // Construir la parte de actualización
        $actualizaciones = [];
        foreach ($atributos as $campo => $valor) {
            $actualizaciones[] = "$campo = VALUES($campo)";
        }
        $query .= join(', ', $actualizaciones);
        //debuguear($query);
        $resultado = self::$db->query($query);
        // Agregar la cláusula ON DUPLICATE KEY UPDATE
        return [
           'resultado' =>  $resultado
        ];
    }

    // Busca un registro por su id
    public function buscarRegistroPeriodo($columna, $valor , $columna2 ,$valor2)
    {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE {$columna} = '{$valor}' and {$columna2} = '{$valor2}'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }



}
?>