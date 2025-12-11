<?php 

    namespace Model;
    class AlumnoDetalles extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'alumnos';
    protected static $columnasDB = ['id', 'nombre_Alumno', 'apellido_Paterno', 'apellido_Materno', 'comentarios', 'nombre_Carrera', 'telefono', 'correo_institucional', 'genero'];
    protected static $aliasColumnasDB = [
    'alumnos.id',
    'alumnos.nombre_Alumno',
    'alumnos.apellido_Paterno',
    'alumnos.apellido_Materno',
    'alumnos.comentarios',
    'alumnos.telefono',
    'alumnos.correo_institucional',
    'carreras.nombre_Carrera',
    'alumnos.genero'
    ];


    public $id;
    public $nombre_Alumno;
    public $apellido_Paterno;
    public $apellido_Materno;
    public $comentarios;
    public $telefono;
    public $correo_institucional;
    public $nombre_Carrera;
    public $genero;

    public function __construct($args = [])
    {
        $this->id = isset($args['id']) ? trim($args['id']) : NULL;
        $this->nombre_Alumno = isset($args['nombre_Alumno']) ? trim($args['nombre_Alumno']) : '';
        $this->apellido_Paterno = isset($args['apellido_Paterno']) ? trim($args['apellido_Paterno']) : '';
        $this->apellido_Materno = isset($args['apellido_Materno']) ? trim($args['apellido_Materno']) : '';
        $this->comentarios = isset($args['comentarios']) ? trim($args['comentarios']) : '';
        $this->nombre_Carrera = isset($args['nombre_Carrera']) ? trim($args['nombre_Carrera']) : '';  
        $this->telefono = isset($args['telefono']) ? (int)$args['telefono'] : '';  
        $this->correo_institucional = isset($args['correo_institucional']) ? trim($args['correo_institucional']) : '';  
        $this->genero = isset($args['genero']) ? trim($args['genero']) : '';  
    }

    public static function buscarConCarrera($columna, $valor, $registros_por_pagina = 10, $offset = 0) 
    {
        // Seguridad básica: solo permitir columnas conocidas
        $columnaPermitida = in_array($columna, static::obtenerColumnas()) ? $columna : null;
        if (!$columnaPermitida) return [];

        // Escapar el nombre de la columna para evitar ambigüedad
        $columnaSQL = "alumnos." . self::$db->real_escape_string($columna);

        // Escapar el valor
        $valorSQL = self::$db->real_escape_string($valor);

        // Construir consulta con JOIN + WHERE + paginación
        $query = "SELECT 
                    alumnos.id, 
                    alumnos.nombre_Alumno, 
                    alumnos.apellido_Paterno, 
                    alumnos.apellido_Materno, 
                    alumnos.comentarios, 
                    alumnos.id_Carrera,
                    alumnos.genero,
                    carreras.nombre_Carrera 
                FROM alumnos 
                LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id 
                WHERE {$columnaSQL} LIKE '%{$valorSQL}%'
                ORDER BY alumnos.id DESC 
                LIMIT {$registros_por_pagina} OFFSET {$offset}";

        return self::consultarSQL($query);
    }
public static function contarFiltrados($columna, $valor) {
    // Agregar espacio al inicio para evitar error de sintaxis

    $join = '';
    if (strpos($columna, 'carreras.') !== false) {
        $join = ' LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id';
    }

    // Corregir columna si viene sin prefijo (opcional)
    if ($columna === 'nombre_Carrera') {
        $columna = 'carreras.nombre_Carrera';
        $join = ' LEFT OUTER JOIN carreras ON alumnos.id_Carrera = carreras.id';
    }

    $esNumerico = $columna === 'alumnos.id';
    $valorSanitizado = $esNumerico ? intval($valor) : addslashes($valor);
    $where = $esNumerico 
        ? "{$columna} = {$valorSanitizado}" 
        : "{$columna} LIKE '%{$valorSanitizado}%'";

    $query = "SELECT COUNT(*) as total 
              FROM alumnos 
              {$join}
              WHERE {$where}";
    // Usar conexión directa
    $resultado = self::$db->query($query);
    $fila = $resultado->fetch_assoc();
    return $fila['total'] ?? 0;

}





}
