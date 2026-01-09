<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Responder inmediatamente a solicitudes preflight
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400'); // 24 horas
    exit;
}

// Permitir solicitudes reales
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Inicializar Eloquent
require __DIR__ . '/../app/Config/eloquent.php';

// Cargar rutas
require __DIR__ . '/../app/Routes/api.php';


// Configurar el paginador (requiere una implementación de Request)
Paginator::currentPageResolver(function ($pageName = 'page') {
    return (int) ($_GET[$pageName] ?? 1);
});
