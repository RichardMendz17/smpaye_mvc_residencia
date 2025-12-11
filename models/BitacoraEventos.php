<?php 

    namespace Model;
    class BitacoraEventos extends ActiveRecord
    {

    // Base de datos
    protected static $tabla = 'bitacora_eventos';
    protected static  $columnasDB = ['id_Usuario', 'tipo_Operacion'];

    public $id_Usuario;
    public $tipo_Operacion;

        
    public function __construct($args = []){
        $this->id_Usuario = $args['id'] ?? NULL;
        $this->tipo_Operacion = isset($args['tipo_Operacion']) ? trim($args['tipo_Operacion']) : '';
    } 

    // Eventos
    public static function eventos($codigo, $id_registro, $nombre_tabla) {
        $mensaje = '';
        $id_usuario = $_SESSION['id'];
        switch ($codigo) {
            case 1:
                $mensaje = "Creación del registro con el id {$id_registro}, perteneciente a la tabla {$nombre_tabla}";
                $query = "INSERT INTO bitacora_eventos (`id_Usuario`, `tipo_Operacion`) VALUES ( {$id_usuario}, '$mensaje')";
                $resultado = BitacoraEventos::$db->query($query);
                return $resultado;
                break;
            case 2:
                $mensaje = "Actualización del registro con el id {$id_registro}, perteneciente a la tabla {$nombre_tabla}";
                $query = "INSERT INTO bitacora_eventos (`id_Usuario`, `tipo_Operacion`) VALUES ( {$id_usuario}, '$mensaje')";
                $resultado = BitacoraEventos::$db->query($query);
                return $resultado;                    
                break;
            case 3:
                $mensaje = "Eliminación del registro con el id {$id_registro}, perteneciente a la tabla {$nombre_tabla}";
                $query = "INSERT INTO bitacora_eventos (`id_Usuario`, `tipo_Operacion`) VALUES ( {$id_usuario}, '$mensaje')";
                $resultado = BitacoraEventos::$db->query($query);
                return $resultado;                    
                break;              
            default: 
                $mensaje = false;
                break;      
        }
        return $mensaje;
    }

    public static function eventoInforme($conceptodepago, $periodo, $Nombre_carrera, $concepto_id, $periodo_id, $carrera_id)
    {
        $mensaje = '';
        $id_usuario = $_SESSION['id'];

        $mensaje = "Consulta de informe para el concepto \'{$conceptodepago}\', el periodo \'{$periodo}\' y la carrera \'{$Nombre_carrera}\', correspondientes a los siguientes IDs: Concepto ({$concepto_id}), Periodo ({$periodo_id}), Carrera ({$carrera_id}).";

        $query = "INSERT INTO bitacora_eventos (`id_Usuario`, `tipo_Operacion`) VALUES ( {$id_usuario}, '$mensaje')";

        $resultado = BitacoraEventos::$db->query($query);
            return $resultado;
    }

    // public static function cursoRequisitos($id_curso, $_cantidad_cursos_requeridos, $caN, $cId, $pId, $caId)
    // {
    //     $mensaje = '';
    //     $id_usuario = $_SESSION['id'];

    //     $mensaje = "Consulta de informe para el concepto \'{$cN}\', el periodo \'{$pMY}\' y la carrera \'{$caN}\', correspondientes a los siguientes IDs: Concepto ({$cId}), Periodo ({$pId}), Carrera ({$caId}).";

    //     $query = "INSERT INTO bitacora_eventos (`id_Usuario`, `tipo_Operacion`) VALUES ( {$id_usuario}, '$mensaje')";

    //     $resultado = BitacoraEventos::$db->query($query);
    //         return $resultado;
    // }
    }
?>