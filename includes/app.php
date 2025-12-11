<?php 

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';

define('CARPETA_EXPORTS', dirname(__DIR__) . '/storage/exports');

if (!is_dir(CARPETA_EXPORTS)) {
    mkdir(CARPETA_EXPORTS, 0777, true);  // true para crear carpetas padre si no existen
}


// Conectarnos a la base de datos
use Model\ActiveRecord;
ActiveRecord::setDB($db);