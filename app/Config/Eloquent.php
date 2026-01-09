<?php
use Illuminate\Database\Capsule\Manager as Capsule;

// Configuración de conexión
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => env('DB_CONNECTION'), 'mysql',
    'host'      => env('DB_HOST'), '127.0.0.1',
    'database'  => env('DB_NAME'),
    'username'  => env('DB_USER'),
    'password'  => env('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Hacer que Eloquent esté disponible globalmente
$capsule->setAsGlobal();
$capsule->bootEloquent();
