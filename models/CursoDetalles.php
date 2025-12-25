<?php 

    namespace Model;
    class CursoDetalles extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'cursos';
    protected static $columnasDB = [
        'id',
        'curso_url',
        'nombre_encargado',
        'nombre_curso',
        'periodo',
        'nombre_Aula',
        'inscripcion_alumno',
        'limite_alumnos',
        'estado'
    ];

    public $id;
    public $curso_url;
    public $nombre_encargado;
    public $nombre_curso;
    public $periodo;
    public $nombre_Aula;
    public $inscripcion_alumno;
    public $limite_alumnos;
    public $estado;

    public function __construct()
    {
        $this->id = $args['id'] ?? null;
        $this->nombre_encargado = isset($args['nombre_encargado']) ? trim($args['nombre_encargado']) : '';
        $this->nombre_curso = isset($args['nombre_curso']) ? trim($args['nombre_curso']) : '';
        $this->periodo = isset($args['periodo']) ? trim($args['periodo']) : '';
        $this->nombre_Aula = isset($args['nombre_Aula']) ? trim($args['nombre_Aula']) : '';
        $this->curso_url = isset($args['curso_url']) ? trim($args['curso_url']) : '';
        $this->limite_alumnos = isset($args['limite_alumnos']) ? trim($args['limite_alumnos']) : '';
        $this->estado = isset($args['estado']) ? trim($args['estado']) : '';
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
