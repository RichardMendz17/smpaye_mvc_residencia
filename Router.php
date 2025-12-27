<?php

namespace MVC;
class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];
    private array $publicRoutes = [
        'GET' => ['/', '/login', '/logout' ],
        'POST' => ['/', '/login', '/logout']
    ];
    private array $protectedFreeRoutes = [
        '/cambiar-rol',
        '/seleccionar-rol'
    ];

    private array $protectedRolesRoutes = [
        //-> Estudiante
        'student' => [
            // Enlace Perfil y dashboard
            '/dashboard', '/perfil', '/cambiar-password',

            // Rutas de actividades estraescolares
            '/actividades-extraescolares',
            '/curso-actividades-extraescolares',

            // Api de alumnos del curso
            '/api/alumnos-curso',

            //Api de Horario del curso
            '/api/horario-curso',

            // Api de boleta de curso
            '/api/boleta-alumno',
            '/api/generar-boleta-alumno',
        ],

        ///-> Jefe de Carrera
        'career_manager' => [
            // Enlace Perfil y dashboard
            '/dashboard', '/perfil',  '/cambiar-password',
            '/calificaciones-alumno'
        ],

        // Coordinadores de cada modulo y su subordinado

        //-> Coordinador de Actividades Extraescolares
        'extracurricular_activities_coordinator' => [
            // Enlace Perfil y dashboard
            '/dashboard', '/perfil', '/cambiar-password',

            // Enlace calificaciones
            '/calificaciones-alumno',

            // Enlace actividades extraescolares cursos
            '/actividades-extraescolares',

            // Enlace CRUD CURSO actividades extraescolaes
            '/crear-curso-actividades-extraescolares', '/curso-actividades-extraescolares', 
            '/curso-actualizar-actividades-extraescolares', '/curso-eliminar-actividades-extraescolares',
            '/curso-actualizar-estado-actividades-extraescolares',

            // Importar Cursos de Actividades Extraescolares
            '/importar-curso-actividades-extraescolares',

            // Enlace CRUD tipos de actividades extraescolares 
            '/tipos-curso', '/tipos-curso-crear', '/tipos-curso-actualizar', 
            '/tipos-curso-buscar', '/tipos-curso-eliminar',

            // Api Alumnos
            '/api/alumnos-curso-eliminar', '/api/alumnos-curso-calificar', '/api/alumnos-curso', 
            '/api/alumnos-buscar', '/api/alumnos-agregar',

            // Api del horario
            '/api/horario-curso', '/api/horario-curso-agregar',  '/api/horario-curso-eliminar',
            // Ver Periodos
            '/periodos'
        ],

        //-> Instructor de actividades extraescolares
        'extracurricular_activities_instructor' => [
            // Enlace Perfil y dashboard
            '/dashboard', '/perfil', '/cambiar-password',

            // Enlace calificaciones
            '/calificaciones-alumno',

            // Enlace actividades extraescolares cursos
            '/actividades-extraescolares',

            // Enlace a CURSO actividades extraescolaes
            '/curso-actividades-extraescolares', 

            // Api Alumnos
            '/api/alumnos-curso-calificar', '/api/alumnos-curso', 

            // Api del horario
            '/api/horario-curso'

        ],

        // -> Coordinador de Créditos Complementarios
        'complementary_credits_coordinator' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password', '/tipos-curso'
        ],
            // Supervisor de creditos complementarios
            'complementary_credits_supervisor' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
            ],

        // Coordinador de Residencias Profesionales
        'professional_residency_coordinator' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
        ],

            // Asesor interno de residencia profesional
            'internal_professional_residency_advisor' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
            ],
            // Asesor externo de residencia profesional
            'external_professional_residency_advisor' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
            ],
        
        // Coordinador de lenguas extranjeras
        'foreign_languages_coordinator' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
        ],
            // Docente de ingles
            'english_teacher' => [
            '/dashboard', '/dashboard', '/curso', '/perfil', '/api/alumnos-curso', '/api/alumnos-curso-calificar', '/cambiar-password', '/api/horario-curso'
            ],

        // Responsable de Caja
        'cashier_responsible' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
        ],

            // Operador de caja
            'cashier_operator' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
            ],

        // Coordinador de titulaciones
        'degree_programs_coordinator' => [
            '/dashboard', '/perfil', '/calificaciones-alumno', '/cambiar-password'
        ],
 
    ];
    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        $auth = $_SESSION['login'] ?? false;

        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];


        $fn = $method === 'GET' ? ($this->getRoutes[$currentUrl] ?? null) : ($this->postRoutes[$currentUrl] ?? null);
        
        $this->checkAccess($currentUrl, $auth, $method);        
        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    private function checkAccess(string $currentUrl, bool $auth, string $method): void
    {

        if ($auth && $currentUrl === '/' )
        {
        // Si ya inicio sesión y trata regresar a la ruta de iniciar sesion lo redirecciona
        header('Location: /perfil');
        exit();
        }

        // Revisamos si la ruta actual esta en las rutas publicas
        if (in_array($currentUrl, $this->publicRoutes[$method] ?? [])) 
        {
            return;
        }

        if (!$auth)
        {
        // Si no ha iniciado sesión y trata de entrar a una ruta protegida
        header('Location: /');
        exit();
        }

        // Si es admin, no se le restringe ninguna ruta
        if (es_admin()) return;

    
        // Verificar que la ruta esté en las rutas libres para usuarios autenticados para el tipo de usuario 2
        $tipo_usuario = $_SESSION['tipo_usuario'];
        if ($tipo_usuario === 2) 
            {
                if (in_array($currentUrl, $this->protectedFreeRoutes))
                {
                    return;                  
                } 
       
            }


        // Si no es admin capturamos el valor de la variable rol
        $rol = $_SESSION['rol'] ?? null;
        // En este punto el usuario ya se autentico y debieron declararse las variables de sesion
        // Si no tiene ningun rol por defecto vamos verificar que tenga algun rol asignado
        if ($rol === null && $_SESSION['login'] === true)
        {
            // Si es la primera vez que inicio sesion la siguiente variable debe estar en false
            if ($_SESSION['cambiando_rol'] === false)
            {
                //routesAllowedWithoutRole
                $routesAllowedWithoutRole = [        
                    '/verificar-tipo-usuario', '/verificar-rol', '/seleccionar-rol', '/cambiar-rol'
                ];
                // Filtramos por el tipo de usuario para
                $tipo_usuario = $_SESSION['tipo_usuario'];
                switch ($tipo_usuario)
                {    // Tipo de usuario 1 para alumno
                    case 1:
                        if (!in_array($currentUrl, $routesAllowedWithoutRole))
                        {
                            header('location: /verificar-tipo-usuario');
                            exit;
                        }
                    break;
                    // Tipo de usuario 2 para personal en general
                    case 2:
                        if (!in_array($currentUrl, $routesAllowedWithoutRole))
                        {
                            header('location: /verificar-tipo-usuario');
                            exit;
                        }                 
                    break;

                    default:

                    break;
                }
            }

        }

        // isset() verifica que la variable exista y no sea null devuelve true.
        // !isset() niega eso: verifica que no exista.
        if (isset($_SESSION['rol'])  && $_SESSION['login'] === true ) 
        {
            // Mapeamos el número de rol a string
            $mapaRoles = [
                0 => 'admin',
                1 => 'student',
                2 => 'career_manager',
                3 => 'extracurricular_activities_coordinator',
                4 => 'extracurricular_activities_instructor',
                5 => 'complementary_credits_coordinator',
                6 => 'complementary_credits_supervisor',
                7 => 'professional_residency_coordinator',
                8 => 'internal_professional_residency_advisor',
                9 => 'external_professional_residency_advisor',
                10 => 'foreign_languages_coordinator',
                11 => 'english_teacher',
                12 => 'cashier_responsible',
                13 => 'cashier_operator',
                14 => 'degree_programs_coordinator',
            ];
            $rolNombre = $mapaRoles[$rol] ?? null;

            if (!$rolNombre || !in_array($currentUrl, $this->protectedRolesRoutes[$rolNombre] ?? []))
            {
                // No tiene permiso
                header('Location: /no-autorizado');
                exit();
            }   
        }         


    }

    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}
