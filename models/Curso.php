<?php 

namespace Model;

use Model\ActiveRecord;

class Curso extends ActiveRecord
{
    protected static $tabla = 'cursos';
    protected static $columnasDB = [
        'id',
        'tipo_curso_id',
        'url',
        'periodo_id',
        'aula_id',
        'encargado_id',
        'inscripcion_alumno',
        'limite_alumnos',
        'estado',
        'requisitos'
    ];

    public $id;
    public $tipo_curso_id;
    public $url;
    public $periodo_id;
    public $aula_id;
    public $encargado_id;
    public $inscripcion_alumno;
    public $limite_alumnos;
    public $estado;
    public $requisitos;



    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->tipo_curso_id = $args['tipo_curso_id'] ?? '';
        $this->url = $args['url'] ?? '';
        $this->periodo_id = $args['periodo_id'] ?? '';
        $this->aula_id = $args['aula_id'] ?? '';
        $this->encargado_id = $args['encargado_id'] ?? '';
        $this->inscripcion_alumno = $args['inscripcion_alumno'] ?? '';
        $this->limite_alumnos = $args['limite_alumnos'] ?? '';
        $this->estado = $args['estado'] ?? '';
        $this->requisitos = $args['requisitos'] ?? '';
    }

    public function validarCurso()
    {
        if (campoVacio($this->encargado_id))
        {
            self::$alertas['error'][] = 'El Encargado del curso es obligatorio';
        }
        if (campoVacio($this->aula_id))
        {
            self::$alertas['error'][] = 'El Aula del curso es obligatorio';
        }        
        if (campoVacio($this->tipo_curso_id))
        {
            self::$alertas['error'][] = 'El Tipo de Actividad extraescolar del curso es obligatorio';
        }
        if (campoVacio($this->periodo_id))
        {
            self::$alertas['error'][] = 'El Periodo del curso es obligatorio';
        }
        // Validamos el ivalor de inscripcion
        if ($this->inscripcion_alumno != 'No Permitido'  && $this->inscripcion_alumno != 'Permitido')
        {
            self::$alertas['error'][] = 'Inscripcion del alumno invalido';
        }
        // Validamos el ivalor de estado del curso
        if ($this->estado != 'Creado'  && $this->estado != 'Abierto' && $this->estado != 'Cerrado' && $this->estado != 'Suspendido')
        {
            self::$alertas['error'][] = 'Estado del Curso invalido';
        }
        // Dos validaciones para la validacion del limite de alumnos
        if ($this->limite_alumnos == '')
        {
            $this->limite_alumnos = Null;
        }
        if ($this->limite_alumnos != Null)
        {
            if ($this->limite_alumnos <= 0)
            {
                self::$alertas['error'][] = 'Limite de alumnos invalido ';
            }
        }
        // Validamos el valor para el campo requisitos
        if ($this->requisitos != 'Si'  && $this->requisitos != 'No')
        {
            self::$alertas['error'][] = 'Valor para requisitos invalido';
        }
        return self::$alertas;
    }
    public static function contarPorPeriodoYRol($periodo_id, $rol, $persona_id)
    {
        $where = "WHERE cursos_actividades_extraescolares.periodo_id = {$periodo_id}";

        if ($rol == 4) {
            $where .= " AND instructor_id = {$persona_id}";
        }

        $sql = "SELECT COUNT(*) as total
                FROM cursos_actividades_extraescolares
                {$where}";
        
        $resultado = self::consultarSQL($sql);
        return $resultado[0]->total ?? 0;
    }    

}
?>