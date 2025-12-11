<?php 

    namespace Model;
    class UsuarioDetalle extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'email', 'password', 'persona_id', 'tipo_usuario'];
    protected static $aliasColumnasDB = [
    'usuarios.id',
    'usuarios.email',
    'usuarios.password',
    'usuarios.persona_id',
    'usuarios.tipo_usuario'
    
    ];

    public $id;
    public $email;
    public $password;
    public $rol;
    public $persona_id;
    public $tipo_usuario;

    public function __construct($args = [])
    {
        $this->id = isset($args['id']) ? trim($args['id']) : NULL;
        $this->email = isset($args['email']) ? trim($args['email']) : '';
        $this->password = isset($args['password']) ? trim($args['password']) : '';
        $this->persona_id = isset($args['persona_id']) ? trim($args['persona_id']) : NULL;
        $this->tipo_usuario = isset($args['tipo_usuario']) ? (int)$args['tipo_usuario'] : '';
    }






}
