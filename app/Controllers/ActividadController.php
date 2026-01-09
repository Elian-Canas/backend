<?php

namespace App\Controllers;

use App\Models\Actividad;
use App\Helpers\Response;

class ActividadController
{
    public function index()
    {
        $actividades = Actividad::limit(50)->get();
        Response::json($actividades);
    }

    public function search()
    {
        $query = $_GET['query'] ?? '';

        $actividades = Actividad::where('producto', 'LIKE', "%$query%")
            ->orWhere('clase', 'LIKE', "%$query%")
            ->select('id', 'clase', 'producto')
            ->limit(100)
            ->get();

        Response::json($actividades);
    }


    public function show($id)
    {
        try {
            $actividad = Actividad::find($id);

            if (!$actividad) {
                return Response::error("Actividad no encontrada", 404);
            }

            return Response::json($actividad);
        } catch (Exception $e) {
            return Response::error("Error al obtener la actividad: " . $e->getMessage(), 500);
        }
    }
}
