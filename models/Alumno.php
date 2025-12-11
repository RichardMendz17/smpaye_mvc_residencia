<?php 

    namespace Model;
    class Alumno extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'alumnos';
    protected static  $columnasDB = ['id', 'nombre_Alumno', 'apellido_Paterno', 'apellido_Materno', 'comentarios', 'id_Carrera', 'telefono', 'correo_institucional', 'genero'];

    public $id;
    public $nombre_Alumno;
    public $apellido_Paterno;
    public $apellido_Materno;
    public $comentarios;
    public $id_Carrera;
    public $telefono;
    public $correo_institucional;
    public $genero;
        
    public function __construct($args = []){
        $this->id = isset($args['id']) ? trim($args['id']) : NULL;
        $this->nombre_Alumno = isset($args['nombre_Alumno']) ? trim($args['nombre_Alumno']) : '';
        $this->apellido_Paterno = isset($args['apellido_Paterno']) ? trim($args['apellido_Paterno']) : '';
        $this->apellido_Materno = isset($args['apellido_Materno']) ? trim($args['apellido_Materno']) : '';
        $this->comentarios = isset($args['comentarios']) ? trim($args['comentarios']) : '';
        $this->id_Carrera = isset($args['id_Carrera']) ? trim($args['id_Carrera']) : '';
        $this->telefono = isset($args['telefono']) ? (int) $args['telefono'] : '';  
        $this->correo_institucional = isset($args['correo_institucional']) ?trim($args['correo_institucional']) : '';  
        $this->genero = isset($args['genero']) ? trim($args['genero']) : '';  
    } 

    public function validar(){
        if(!$this->id){
            self::$alertas['error'][] = 'El Número de control es obligatorio';
        } else{
            if(!preg_match('/[0-9]{8}/', $this->id)){
                self::$alertas['error'][] = "Formato del Número de control no válido";
            } 
        }
        if(!$this->nombre_Alumno){
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if(!$this->apellido_Paterno){
            self::$alertas['error'][] = 'El Apellido Paterno es obligatorio';
        }
        if(!$this->apellido_Materno){
            self::$alertas['error'][] = 'El Apellido Materno es obligatorio';
        }    
        if ($this->id_Carrera === '' || $this->id_Carrera === null) {
            self::$alertas['error'][] = 'La Carrera es obligatoria';
        }
        if(!$this->genero){
            self::$alertas['error'][] = 'El Genero es obligatorio';
        } 
        return self::$alertas;
    }

   // Método para devolver el nombre completo
    public function nombre_completo() {
        return "{$this->nombre_Alumno} {$this->apellido_Paterno} {$this->apellido_Materno}";
    }

    public function guardarAlumno(){
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ('"; 
        $query .= join("','", array_values($atributos));
        $query .= "') ";
        // Resultado de la consulta
        $resultado = self::$db->query($query);
        //debuguear($query);
        return [
           'resultado' =>  $resultado
        ];
    }

    public function actualizarAlumno(){
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

}
?>