<?php 

namespace Model;

use Model\TipoUsuario;
use Model\Alumno;
use Model\Personal;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','email', 'password', 'rol',  'persona_id', 'tipo_usuario'];

    public $id;
    public $email;
    public $password;
    public $rol;
    public $persona_id;
    public $tipo_usuario;
    public $confirm_password;
    public $password_actual;
    public $password_nuevo;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->email = isset($args['email']) ? trim($args['email']) : '';
        $this->persona_id = isset($args['persona_id']) ? trim($args['persona_id']) : '';
        $this->tipo_usuario = isset($args['tipo_usuario']) ? (int)$args['tipo_usuario'] : '';
        $this->rol = isset($args['rol']) ? trim($args['rol']) : null;      
        $this->password = $args['password'] ?? '';
        $this->confirm_password = $args['confirm_password'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';

    }

        //Validar login
    public function validarLogin(){
        if(!$this->email)
        {
            self::$alertas['error'][] = 'El Email del usuario es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no válido';
        }        
        if(!$this->password)
        {
            self::$alertas['error'][] = 'El Password del usuario es obligatorio';
        }
        return self::$alertas;
    }

        //Validación para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->email)
        {
            self::$alertas['error'][] = 'El Email del usuario es obligatorio';
        }
        if(!$this->password)
        {
            self::$alertas['error'][] = 'El Password del usuario es obligatorio';
        }
        if(strlen($this->password) < 6)
        {
            self::$alertas['error'][]= 'El Password debe contener al menos 6 caracteres';
        }
           
        return self::$alertas;
    }

        //Valida un email
    public function validarEmail(){
        if(!$this->email)
        {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL))
        {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }

        // Valida el Password
    public function validarPassword()
    {
        if (!$this->password)
        {
            self::$alertas['error'][] = 'El password no puede ir vacío';
        }
        if(strlen($this->password) < 6)
        {
            self::$alertas['error'][]= 'El Password debe contener al menos 6 caracteres';
        }     
    }

    public function validar_perfil(){
        if (!$this->email) {
            self::$alertas['error'][]= ' El Email es Obligatorio';
        }
        return self::$alertas;        
    }

    public function nuevo_password() : array
    {
        if (!$this->password_actual)
        {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacío';
        }
        if (!$this->password_nuevo)
        {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacío';
        }
        if ($this->password_nuevo && strlen($this->password_nuevo) < 6)
        {
            self::$alertas['error'][] = 'El Password debe contener al menos 6';
        }
        return self::$alertas;
    }

        // Comprobar el password
    public function comprobar_password() : bool
    {
        return password_verify($this->password_actual, $this->password);
    }

        // Hashea el password
    public function hashPassword() : void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function obtenerTipoUsuario() 
    {
        $tipo_usuario = TipoUsuario::find($this->tipo_usuario);
        return $tipo_usuario ? $tipo_usuario->id : '';
        
    }

    public function obtenerNombreReal()
    {
        // Si es Alumno buscar en el modelo de alumno'
        if ($this->tipo_usuario === 1) 
        {
            $alumno = Alumno::find($this->persona_id);
            return $alumno ? $alumno->nombre_completo() : ''; 
        }
        // Si es personal del instituto
        else if ($this->tipo_usuario  === 2)
        {
            $persona = Personal::find($this->persona_id);
            return $persona ? $persona->nombre_completo() : ''; 
        }
    }



}



?>