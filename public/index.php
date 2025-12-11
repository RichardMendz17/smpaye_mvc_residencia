<?php 
session_start();
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AulasController;
use Controllers\LoginController;
use Controllers\PerfilController;
use Controllers\AlumnosController;
use Controllers\CarrerasController;
use Controllers\PeriodosController;
use Controllers\UsuariosController;
use Controllers\DashboardController;
use Controllers\ApiAlumnosController;
use Controllers\ApiUsuariosController;
use Controllers\CalificacionesController;
use Controllers\ApiBoletaAlumnoController;
use Controllers\InglesDashboardController;
use Controllers\ApiHorariosClaseController;
use Controllers\ArchivosUsuariosController;
use Controllers\PersonalController;
use Controllers\ActividadesExtraescolaresTiposController;
use Controllers\ActividadesExtraescolaresDashboardController;
use Controllers\ApiPersonalController;
use Controllers\AsignacionRolesController;
use Controllers\SeleccionarRolController;
use Controllers\TiposCursosController;
use Controllers\VerificarRolController;
use Controllers\VerificarTipoUsuarioController;

$router = new Router();

//Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Verificar tipo de usuario
$router->get('/verificar-tipo-usuario', [VerificarTipoUsuarioController::class, 'index']);
$router->post('/verificar-tipo-usuario', [VerificarTipoUsuarioController::class, 'index']);

// Verificar rol
$router->get('/verificar-rol', [VerificarRolController::class, 'index']);
$router->post('/verificar-rol', [VerificarRolController::class, 'index']);

//Seleccionar Rol
$router->get('/seleccionar-rol', [SeleccionarRolController::class, 'index']);
$router->post('/seleccionar-rol', [SeleccionarRolController::class, 'index']);

// Seleccionamos un rol por defecto 
$router->get('/seleccionar-rol-por-defecto', [SeleccionarRolController::class, 'seleccionar_rol_por_defecto']);
$router->post('/seleccionar-rol-por-defecto', [SeleccionarRolController::class, 'seleccionar_rol_por_defecto']);

// Quitamos el rol
$router->post('/cambiar-rol', [SeleccionarRolController::class, 'quitar_rol']);
$router->get('/cambiar-rol', [SeleccionarRolController::class, 'quitar_rol']);

// Calificaciones
$router->get('/calificaciones-alumno', [CalificacionesController::class, 'index']);
$router->post('/calificaciones-alumno', [CalificacionesController::class, 'index']);

// Página de inicio
$router->get('/dashboard', [DashboardController::class, 'index']);


// Dashboard de ingles
$router->get('/ingles', [InglesDashboardController::class, 'index']);
$router->get('/crear-curso-ingles', [InglesDashboardController::class, 'crear_curso']);
$router->post('/crear-curso-ingles', [InglesDashboardController::class, 'crear_curso']);
$router->get('/curso-ingles', [InglesDashboardController::class, 'curso']);
$router->get('/cursos-actualizar-ingles', [InglesDashboardController::class, 'actualizar']);
$router->post('/cursos-actualizar-ingles', [InglesDashboardController::class, 'actualizar']);
$router->post('/cursos-eliminar-ingles', [InglesDashboardController::class, 'eliminar_Curso']);

//// CRUD de tipos de cursos
$router->get('/tipos-curso', [TiposCursosController::class, 'index']);
$router->get('/tipos-curso-crear', [TiposCursosController::class, 'crear']);
$router->post('/tipos-curso-crear', [TiposCursosController::class, 'crear']);
$router->get('/tipos-curso-actualizar', [TiposCursosController::class, 'actualizar']);
$router->post('/tipos-curso-actualizar', [TiposCursosController::class, 'actualizar']);
$router->get('/tipos-curso-buscar', [TiposCursosController::class, 'buscar']);
$router->post('/tipos-curso-buscar', [TiposCursosController::class, 'buscar']);
$router->post('/tipos-curso-eliminar', [TiposCursosController::class, 'eliminar']);

// Dashboard de Actividades Complementarias
$router->get('/actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'index']);
$router->get('/crear-curso-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'crear_curso']);
$router->post('/crear-curso-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'crear_curso']);
$router->get('/curso-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'curso']);
$router->get('/curso-actualizar-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'actualizar']);
$router->post('/curso-actualizar-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'actualizar']);
$router->post('/curso-actualizar-estado-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'actualizar_estado_curso']);
$router->post('/curso-eliminar-actividades-extraescolares', [ActividadesExtraescolaresDashboardController::class, 'eliminar_Curso']);

//// CRUD de tipos de actividades extraescolares
$router->get('/tipos-actividades-extraescolares', [TiposCursosController::class, 'index']);
$router->get('/actividades-extraescolares-crear', [TiposCursosController::class, 'crear']);
$router->post('/actividades-extraescolares-crear', [TiposCursosController::class, 'crear']);
$router->get('/actividades-extraescolares-actualizar', [TiposCursosController::class, 'actualizar']);
$router->post('/actividades-extraescolares-actualizar', [TiposCursosController::class, 'actualizar']);
$router->get('/actividades-extraescolares-buscar', [TiposCursosController::class, 'buscar']);
$router->post('/actividades-extraescolares-buscar', [TiposCursosController::class, 'buscar']);
$router->post('/actividades-extraescolares-eliminar', [TiposCursosController::class, 'eliminar']);

// Dashboard de Creditos Complementarios
$router->get('/creditos-complementarios', [InglesDashboardController::class, 'index']);
$router->get('/crear-curso-creditos-complementarios', [InglesDashboardController::class, 'crear_curso']);
$router->post('/crear-curso-creditos-complementarios', [InglesDashboardController::class, 'crear_curso']);
$router->get('/curso-creditos-complementarios', [InglesDashboardController::class, 'curso']);
$router->get('/cursos-actualizar-creditos-complementarios', [InglesDashboardController::class, 'actualizar']);
$router->post('/cursos-actualizar-creditos-complementarios', [InglesDashboardController::class, 'actualizar']);
$router->post('/cursos-eliminar-creditos-complementarios', [InglesDashboardController::class, 'eliminar_Curso']);


// Perfil
$router->get('/perfil', [PerfilController::class, 'perfil']);
$router->post('/perfil', [PerfilController::class, 'perfil']);
$router->get('/cambiar-password', [PerfilController::class, 'cambiar_password']);
$router->post('/cambiar-password', [PerfilController::class, 'cambiar_password']);

// Zona de Archivos Usuarios
$router->get('/archivos-usuarios', [ArchivosUsuariosController::class, 'index']);
$router->get('/archivos-usuarios-descargar', [ArchivosUsuariosController::class, 'descargar']);
$router->get('/archivos-usuarios-buscar', [ArchivosUsuariosController::class, 'buscar']);
$router->post('/archivos-usuarios-buscar', [ArchivosUsuariosController::class, 'buscar']);
$router->post('/archivos-usuarios-eliminar', [ArchivosUsuariosController::class, 'eliminar']);


// API para los alumnos
$router->get('/api/alumnos-curso', [ApiAlumnosController::class, 'index']);     // Leer todos
$router->post('/api/alumnos-agregar', [ApiAlumnosController::class, 'agregar_alumnos']);    // Crear
$router->post('/api/alumnos-curso-calificar', [ApiAlumnosController::class, 'asignar_calificacion']); // Actualizar
$router->post('/api/alumnos-curso-referencia', [ApiAlumnosController::class, 'asignar_referencia']); // Actualizar
$router->post('/api/alumnos-curso-eliminar', [ApiAlumnosController::class, 'eliminar']); // Eliminar
$router->get('/api/alumnos-buscar', [ApiAlumnosController::class, 'buscar']);

// API para los horarios de las clases
$router->get('/api/horario-curso', [ApiHorariosClaseController::class, 'index']);     // Leer todos
$router->post('/api/horario-curso-agregar', [ApiHorariosClaseController::class, 'agregar']);    // Crear
$router->post('/api/horario-curso-eliminar', [ApiHorariosClaseController::class, 'eliminar']); // Eliminar

// API para el personal 
$router->get('/api/personal-buscar', [ApiPersonalController::class, 'buscar']);

// API para obtener la boleta del alumno
$router->get('/api/boleta-alumno', [ApiBoletaAlumnoController::class, 'index']);
$router->get('/api/generar-boleta-alumno', [ApiBoletaAlumnoController::class, 'generarBoleta']);

// Perfiles
$router->get('/usuarios', [UsuariosController::class, 'administrar_usuarios']);
$router->get('/usuarios-crear', [UsuariosController::class, 'crear']);
$router->post('/usuarios-crear', [UsuariosController::class, 'crear']);
$router->get('/usuarios-buscar', [UsuariosController::class, 'buscar']);
$router->post('/usuarios-buscar', [UsuariosController::class, 'buscar']);
$router->post('/usuarios-eliminar', [UsuariosController::class, 'eliminar']);

// Asignacion de roles

$router->get('/asignacion-roles', [AsignacionRolesController::class, 'index']);
$router->get('/asignacion-roles-crear', [AsignacionRolesController::class, 'crear']);
$router->post('/asignacion-roles-crear', [AsignacionRolesController::class, 'crear']);
$router->get('/asignacion-roles-actualizar', [AsignacionRolesController::class, 'actualizar']);
$router->post('/asignacion-roles-actualizar', [AsignacionRolesController::class, 'actualizar']);
$router->get('/asignacion-roles-buscar', [AsignacionRolesController::class, 'buscar']);
$router->post('/asignacion-roles-buscar', [AsignacionRolesController::class, 'buscar']);
$router->post('/asignacion-roles-eliminar', [AsignacionRolesController::class, 'eliminar']);

// Api usuarios
$router->post('/api/usuarios-cambiar-password', [ApiUsuariosController::class, 'cambiar_password']);

//// CRUD de alumnos
$router->get('/alumnos', [AlumnosController::class, 'index']);
$router->get('/alumnos-crear', [AlumnosController::class, 'crear']);
$router->post('/alumnos-crear', [AlumnosController::class, 'crear']);
$router->get('/alumnos-importar', [AlumnosController::class, 'importar']);
$router->post('/alumnos-importar', [AlumnosController::class, 'importar']);
$router->get('/alumnos-actualizar', [AlumnosController::class, 'actualizar']);
$router->post('/alumnos-actualizar', [AlumnosController::class, 'actualizar']);
$router->get('/alumnos-buscar', [AlumnosController::class, 'buscar']);
/*$router->post('/alumnos-buscar', [AlumnosController::class, 'buscar']);*/
$router->post('/alumnos-eliminar', [AlumnosController::class, 'eliminar']);

//// CRUD de carreras
$router->get('/carreras', [CarrerasController::class, 'index']);
$router->get('/carreras-crear', [CarrerasController::class, 'crear']);
$router->post('/carreras-crear', [CarrerasController::class, 'crear']);
$router->get('/carreras-actualizar', [CarrerasController::class, 'actualizar']);
$router->post('/carreras-actualizar', [CarrerasController::class, 'actualizar']);
$router->get('/carreras-buscar', [CarrerasController::class, 'buscar']);
$router->post('/carreras-buscar', [CarrerasController::class, 'buscar']);
$router->post('/carreras-eliminar', [CarrerasController::class, 'eliminar']);

//// CRUD de aulas
$router->get('/aulas', [AulasController::class, 'index']);
$router->get('/aulas-crear', [AulasController::class, 'crear']);
$router->post('/aulas-crear', [AulasController::class, 'crear']);
$router->get('/aulas-actualizar', [AulasController::class, 'actualizar']);
$router->post('/aulas-actualizar', [AulasController::class, 'actualizar']);
$router->get('/aulas-buscar', [AulasController::class, 'buscar']);
$router->post('/aulas-buscar', [AulasController::class, 'buscar']);
$router->post('/aulas-eliminar', [AulasController::class, 'eliminar']);

//// CRUD de periodos
$router->get('/periodos', [PeriodosController::class, 'index']);
$router->get('/periodos-crear', [PeriodosController::class, 'crear']);
$router->post('/periodos-crear', [PeriodosController::class, 'crear']);
$router->get('/periodos-actualizar', [PeriodosController::class, 'actualizar']);
$router->post('/periodos-actualizar', [PeriodosController::class, 'actualizar']);
$router->get('/periodos-buscar', [PeriodosController::class, 'buscar']);
/*$router->post('/periodos-buscar', [PeriodosController::class, 'buscar']);*/
$router->post('/periodos-eliminar', [PeriodosController::class, 'eliminar']);

//// CRUD de personal 
$router->get('/personal', [PersonalController::class, 'index']);
$router->get('/personal-crear', [PersonalController::class, 'crear']);
$router->post('/personal-crear', [PersonalController::class, 'crear']);
$router->get('/personal-actualizar', [PersonalController::class, 'actualizar']);
$router->post('/personal-actualizar', [PersonalController::class, 'actualizar']);
$router->get('/personal-buscar', [PersonalController::class, 'buscar']);
/*$router->post('/personal-buscar', [PersonalController::class, 'buscar']);*/
$router->post('/personal-eliminar', [PersonalController::class, 'eliminar']);



$router->comprobarRutas();