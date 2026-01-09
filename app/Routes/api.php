<?php

use App\Controllers\OfertaController;
use App\Controllers\ActividadController;
use App\Controllers\DocumentoController;
use App\Helpers\Response;

// Normalizar método y URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$uri = trim($uri, '/');

// Definir rutas como un array asociativo: [método][patrón] => callable
$routes = [
    'GET' => [
        'ofertas'                     => fn() => (new OfertaController)->index(),
        'ofertas/(\\d+)'            => fn($id) => (new OfertaController)->show((int)$id),
        'ofertas/export'            => fn() => (new OfertaController)->exportExcel(),
        'actividades'                 => fn() => (new ActividadController)->index(),
        'actividades/buscar'          => fn() => (new ActividadController)->search(),
        'actividades/(\\d+)'            => fn($id) => (new ActividadController)->show((int)$id),
    ],
    'POST' => [
        'ofertas'                     => fn() => (new OfertaController)->store(),
        'documentos'                  => fn() => (new DocumentoController)->store(),
    ],
    'PUT' => [
        'ofertas/(\d+)'               => fn($id) => (new OfertaController)->update((int)$id),
    ],
];

// Buscar coincidencia
$routeFound = false;

if (isset($routes[$method])) {
    foreach ($routes[$method] as $pattern => $handler) {
        // Soportar parámetros dinámicos (ej: /ofertas/123)
        if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
            array_shift($matches); // eliminar la coincidencia completa
            $handler(...$matches);
            $routeFound = true;
            break;
        }
    }
}

if (!$routeFound) {
    Response::error('Ruta no encontrada', 404);
}