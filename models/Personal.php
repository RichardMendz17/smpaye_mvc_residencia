<?php 

    namespace Model;
    class Personal extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'personal';
    protected static  $columnasDB = ['id', 'nombre', 'apellido_Paterno', 'apellido_Materno', 'genero'];

    public $id;
    public $nombre;
    public $apellido_Paterno;
    public $apellido_Materno;
    public $genero;

        
    public function __construct($args = []){
        $this->id = isset($args['id']) ? trim($args['id']) : NULL;
        $this->nombre = isset($args['nombre']) ? trim($args['nombre']) : '';
        $this->apellido_Paterno = isset($args['apellido_Paterno']) ? trim($args['apellido_Paterno']) : '';
        $this->apellido_Materno = isset($args['apellido_Materno']) ? trim($args['apellido_Materno']) : '';
        $this->genero = isset($args['genero']) ? trim($args['genero']) : '';
     
    } 

    public function validar(){
        if(campoVacio($this->id))
        {
            self::$alertas['error'][] = 'El Número de la Matrícula es obligatorio';
        } else {
            // Validar que tenga 8 dígitos exactos (o al menos 8)
            if (!preg_match('/^\d{8,}$/', $this->id)) {
                self::$alertas['error'][] = 'El Número de Matrícula debe tener al menos 8 dígitos numéricos, con el primer digito diferente de 0';
            }

            // Validar que no sea '00000000' ni '00000001'
            if (in_array($this->id, ['00000000', '00000001'])) {
                self::$alertas['error'][] = 'El Número de Matrícula no puede ser un valor inválido';
            }
        }
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if(!$this->apellido_Paterno){
            self::$alertas['error'][] = 'El Apellido Paterno es obligatorio';
        }
        if(!$this->apellido_Materno){
            self::$alertas['error'][] = 'El Apellido Materno es obligatorio';
        }    
        if(!$this->genero){
            self::$alertas['error'][] = 'El Genero es obligatorio';
        }   
        return self::$alertas;
    }
    public function guardarPersonal()
    {
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
    public function nombre_completo() {
        return "{$this->nombre} {$this->apellido_Paterno} {$this->apellido_Materno}";
    }

}
?>