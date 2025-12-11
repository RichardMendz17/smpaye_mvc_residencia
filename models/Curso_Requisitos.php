<?php 

namespace Model;

use Model\ActiveRecord;

class Curso_Requisitos extends ActiveRecord
{
    protected static $tabla = 'curso_requisitos';
    protected static $columnasDB = ['id', 'id_curso', 'minimo_aprobados', 'curso_excluido'];

    public $id;
    public $id_curso;
    public $minimo_aprobados;
    public $curso_excluido;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->id_curso = $args['id_curso'] ?? '';
        $this->minimo_aprobados = $args['minimo_aprobados'] ?? '';
        $this->curso_excluido = $args['curso_excluido'] ?? '';
    }

    public function validarCursoRequisitos()
    {
        if (campoVacio($this->id_curso))
        {
            self::$alertas['error'][] = 'El Id del curso es obligatorio';
        }
        if (campoVacio($this->minimo_aprobados))
        {
            self::$alertas['error'][] = 'La cantidad de Cursos minimos aprobados es necesaria';
        }        
        if (campoVacio($this->curso_excluido))
        {
            self::$alertas['error'][] = 'El Tipo de Actividad extraescolar del curso es obligatorio';
        }
         
        return self::$alertas;
    }
    

}
?>