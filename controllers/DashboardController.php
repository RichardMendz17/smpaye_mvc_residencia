<?php 

namespace Controllers;

use MVC\Router;

class DashboardController
{
    public static function index(Router $router)
    {
        isAuth();
        $alertas = [];
        // Render a la vista
        $router->render('dashboard/index',
        [
            'titulo_pagina' => 'Pagina de Inicio2',
            'alertas' => $alertas,
            'sidebar_nav' => null
        ]);
    }
}

?>