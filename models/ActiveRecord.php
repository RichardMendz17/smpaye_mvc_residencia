<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    protected static $aliasColumnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    //valida que un campo este totalmente vacio


    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }
    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!campoVacio($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    public static function obtenerColumnas() {
        return static::$columnasDB;
    }

    // Devuelve las alias para consultas personalizadas
    public static function obtenerColumnasAlias() {
        return static::$aliasColumnasDB;
    }    
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
     public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Obtener Registro
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite} ORDER BY id DESC";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Paginar los registros
    public static function paginar($por_pagina, $offset){
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT {$por_pagina} OFFSET {$offset} ";
        $resultado = self::consultarSQL($query);
        return $resultado;  
    }

    // Paginar los registros con mas atributos
    public static function whereAllPaginado($columna, $valor, $limite, $offset)
    {
        $columna = self::$db->escape_string($columna);
        $valor = self::$db->escape_string($valor);
        $limite = (int)$limite;
        $offset = (int)$offset;

        $query = "SELECT * FROM ". static::$tabla ." WHERE $columna = '$valor' LIMIT $limite OFFSET $offset";
        $resultado = self::SQL($query);
        return $resultado;
    }

    // Busqueda Where con Columna
    // Solo devuelve el primer registro encontrado
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ; 
    }
    //Buscar un registro por multiples columnas
    public static function buscarPorMultiples(array $columnas, array $valores)
    {
        if (count($columnas) !== count($valores)) return null;

        $condiciones = [];
        foreach ($columnas as $index => $columna) {
            $valor = static::$db->escape_string($valores[$index]);
            $condiciones[] = "{$columna} = '{$valor}'";
        }

        $sql = "SELECT * FROM " . static::$tabla . " WHERE " . implode(" AND ", $condiciones) . " LIMIT 1";
        $resultado = static::consultarSQL($sql);
        return array_shift($resultado); // Devuelve el primer objeto encontrado o null
    }

    //Devuelve la cantidad total de los registros encontrados
    public static function whereAllCount($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return (string) count($resultado); // Convertimos el número a string
    }

    // Traer la cantidad total de registros contados
    public static function total(){
        $query = "SELECT COUNT(*) FROM " . static::$tabla;
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift($total);
    }
    // Trae la cantidad total de registros con filtros
    public static function contarConFiltros($query) {
        // Convertimos la consulta SELECT en una consulta COUNT
        $query = preg_replace('/SELECT.*?FROM/i', 'SELECT COUNT(*) as total FROM', $query, 1);
        
        // Eliminamos ORDER BY, LIMIT y OFFSET que no son necesarios para el conteo
        $query = preg_replace('/ORDER BY.*/i', '', $query);
        $query = preg_replace('/LIMIT \d+/i', '', $query);
        $query = preg_replace('/OFFSET \d+/i', '', $query);
        $resultado = self::SQL($query);
        return $resultado[0]->total ?? 0;
    }


    // Busca todos los registros que pertenecen a un id 
    public static function belongsTo($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // SQL para Consultas Avanzadas que retorna un array de objetos
    public static function SQL($consulta) {
        $query = $consulta;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // SQLContar para Consultas Avanzadas.
    public static function SQLContar($consulta) {
        $resultado = self::consultarSQL($consulta); // Esto devuelve un arreglo de objetos
        return (string) count($resultado); // Convertimos el número a string
    }

    // unicoSQL para Consultas Avanzadas. sin devolver un objeto
    public static function unicoSQL($consulta) {
        $query = $consulta;
        $resultado = self::$db->query($query);
        return $resultado;
    }    
    
    // Consultar por un registro y obtener un objeto para no tener que ciclar
    public static function obtenerUnico($query) {
        $resultados = self::consultarSQL($query);
        return $resultados[0] ?? null;
    }

    // crea un nuevo registro
    public function crear() {
        $atributos = $this->sanitizarAtributos();
        
        $columnas = implode(', ', array_keys($atributos));
        $valores = implode(', ', array_map(function($valor) {
            return  is_null($valor) ? 'NULL' : "'" . $valor . "'";
        }, array_values($atributos)));

        $query = "INSERT INTO " . static::$tabla . " ($columnas) VALUES ($valores)";
        //debuguear($query);
        $resultado = self::$db->query($query);
        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }

    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value)
        {
            if ($value === null)
            {
                $valores[] = "{$key}=null";            
            } else 
                {
                    $valores[] = "{$key}='{$value}'";
                }

        }

        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(" ,  ", $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 
        $resultado = self::$db->query($query);
        return [
        'resultado' => $resultado,
        'id' => $this->id 
        ];
    }

    // Eliminar un registro - Toma el ID de Active Record
    public function eliminar()
    {
        try
        {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        
        $resultado = self::$db->query($query);
        return $resultado;
        } catch (\mysqli_sql_exception $e)
            {
            return false;
            //Se aplico un try catch porque algunas fk de tablas tienen resctriction en ambas action
            }       
    }

    public static function consultarSQL($query) {
        // Consultar la base de datos
        //debuguear($query);
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro) {
        $objeto = new static;
        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        
        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id' && !$this instanceof Alumno && !$this instanceof Personal) continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos() 
    {
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach ($atributos as $key => $value) {
            if ($value === null) {
                $sanitizado[$key] = NULL; // sin comillas
            } else {
                $sanitizado[$key] = self::$db->escape_string($value); // solo escape
            }
        }

        return $sanitizado;
    }


    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }
}