<?php

namespace Controllers;

// Para generar informe

use Model\BitacoraEventos;
use Model\Calificaciones;
use MVC\Router;

class CalificacionesController {

    public static function index(Router $router)
    {
        $alertas = [];
        isAuth();
        $calificacion_alumno = null;

        $alertas = Calificaciones::getAlertas();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if ($_POST["num_Control_Alumno"] !== '') 
            {
                $num_Control_Alumno = $_POST["num_Control_Alumno"];
            if (!is_numeric($num_Control_Alumno)) 
            {
                $alertas['error'][] = 'El valor no ingresado no es válido';
            }  else 
                    {
                    $_SESSION['num_Control_Alumno'] = $_POST["num_Control_Alumno"];
                    $query = " SELECT 
                            a.id AS numero_control,
                            CONCAT(a.nombre_Alumno, ' ', a.apellido_Paterno, ' ', a.apellido_Materno) AS nombre_Alumno,
                            n.nombre_Nivel AS curso,
                            CONCAT(d.nombre_Docente, ' ', d.apellido_Paterno, ' ', d.apellido_Materno) AS nombre_Docente,
                            CONCAT(p.meses_Periodo, ' ', p.year) AS periodo,
                            acd.calificacion,
                            acd.estatus,
                            acd.fecha_inscripcion
                        FROM 
                            alumnos a
                        JOIN 
                            alumno_curso_detalles acd ON a.id = acd.alumno_id
                        JOIN 
                            cursos c ON acd.curso_detalle_id = c.id
                        JOIN 
                            niveles n ON c.nivel_id = n.id
                        JOIN 
                            docentes d ON c.docente_id = d.id
                        JOIN 
                            periodos p ON c.periodo_id = p.id
                        WHERE 
                            a.id = {$num_Control_Alumno}
                        ORDER BY 
                            p.year DESC, p.meses_Periodo DESC, n.nombre_Nivel ASC ";

                            $calificacion_alumno = Calificaciones::SQL($query);
                            $alertas = [];

                        if (!$calificacion_alumno) {
                            $alertas['error'][] = 'No hay resultados';
                        }
                    }          
            } else 
                {
                    $alertas['error'][] = 'Llene el campo para realizar la busqueda';
                }
 

        }
        $router->render('calificaciones/index', [
            'titulo_pagina' => 'Calificaciones',
            'sidebar_nav' => 'Calificaciones',               
            'calificacion_alumno' => $calificacion_alumno,
            'alertas' => $alertas,

        ]);
    }

}