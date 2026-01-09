<?php

namespace App\Controllers;

use App\Helpers\GeneratorConsecutive;
use App\Helpers\Response;
use App\Models\Oferta;
use App\Validations\OfertaValidator;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class OfertaController
{
  
  /**
   * Lista todas las ofertas con paginación.
   */
  public function index()
  {
    try {
      // Leer y sanitizar parámetros
      $page = (int) ($_GET['page'] ?? 1);
      $limit = (int) ($_GET['limit'] ?? 15);

      // Validar y limitar valores razonables
      $page = max(1, $page);
      $limit = max(1, min($limit, 100)); // máximo 100 registros por página

      // Iniciar consulta y aplicar filtros
      $query = Oferta::query();
      $this->applyFilters($query);

      $paginator = $query->paginate($limit, ['*'], 'page', $page);

      $response = [
        'data' => $paginator->items(),
        'meta' => [
          'current_page' => $paginator->currentPage(),
          'last_page' => $paginator->lastPage(),
          'per_page' => $paginator->perPage(),
          'total' => $paginator->total(),
          'from' => $paginator->firstItem(),
          'to' => $paginator->lastItem(),
        ]
      ];

      Response::json($response);
    } catch (Exception $e) {
      error_log('Error en OfertaController@index: ' . $e->getMessage());
      Response::error('Error al obtener las ofertas', 500);
    }
  }

  public function store()
  {
    try {
      $data = $this->getJsonInput();

      OfertaValidator::validate($data);

      DB::beginTransaction();

      // Generar consecutivo automáticamente
      $consecutivo = GeneratorConsecutive::generar();

      $oferta = Oferta::create([

        'consecutivo'          => $consecutivo,
        'objeto'          => $data['objeto'],
        'descripcion'     => $data['descripcion'],
        'moneda'          => $data['moneda'],
        'presupuesto'     => $data['presupuesto'],
        'actividad_id'    => $data['actividad_id'],
        'fecha_inicio'    => $data['fecha_inicio'],
        'hora_inicio'     => $data['hora_inicio'],
        'fecha_cierre'    => $data['fecha_cierre'],
        'hora_cierre'     => $data['hora_cierre'],
        'estado'          => 'ABIERTA',
        'creado_en'       => date('Y-m-d H:i:s'),
        'actualizado_en'  => date('Y-m-d H:i:s'),
      ]);

      DB::commit();
      Response::json($oferta, 201);

    } catch (Exception $e) {

      DB::rollBack();
      error_log('Error en OfertaController@store: ' . $e->getMessage());
      Response::error($e->getMessage(), 422);
    }
  }

  public function update($id)
  {
    try {
      $oferta = Oferta::findOrFail($id);
      $data = $this->getJsonInput();

      OfertaValidator::validate($data);

      // Validación: en edición debe existir al menos 1 documento
      if ($oferta->documentos()->count() === 0) {
        throw new Exception('Debe existir al menos un documento cargado');
      }

      $oferta->update([
        'objeto'         => $data['objeto'],
        'descripcion'    => $data['descripcion'],
        'moneda'         => $data['moneda'],
        'presupuesto'    => $data['presupuesto'],
        'actividad_id'   => $data['actividad_id'],
        'fecha_inicio'   => $data['fecha_inicio'],
        'hora_inicio'    => $data['hora_inicio'],
        'fecha_cierre'   => $data['fecha_cierre'],
        'hora_cierre'    => $data['hora_cierre'],
        'estado'         => $data['estado'],
        'actualizado_en' => date('Y-m-d H:i:s'),
      ]);

      Response::json($oferta);
    } catch (Exception $e) {
      error_log('Error en OfertaController@update: ' . $e->getMessage());
      Response::error($e->getMessage(), 422);
    }
  }

  public function show($id)
  {
    try {
        $oferta = Oferta::with('actividad:id,producto')->find($id);

        if (!$oferta) {
            return Response::error("Oferta no encontrada", 404);
        }

        return Response::json($oferta);
    } catch (Exception $e) {
        return Response::error("Error al obtener la oferta: " . $e->getMessage(), 500);
    }
  }

/**
 * Aplica filtros a una consulta de ofertas.
 */
private function applyFilters($query)
{
    // Filtro por consecutivo
    if (!empty($_GET['consecutivo'])) {
        $query->where('consecutivo', 'LIKE', '%' . $_GET['consecutivo'] . '%');
    }

    // Filtro por objeto
    if (!empty($_GET['objeto'])) {
        $query->where('objeto', 'LIKE', '%' . $_GET['objeto'] . '%');
    }

    // Filtro por descripción
    if (!empty($_GET['descripcion'])) {
        $query->where('descripcion', 'LIKE', '%' . $_GET['descripcion'] . '%');
    }

    return $query;
}

  /**
   * Helper para leer cuerpo JSON
   */
  private function getJsonInput(): array
  {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new Exception('Cuerpo de la solicitud no es JSON válido');
    }

    if (!is_array($data)) {
      throw new Exception('El cuerpo debe ser un objeto JSON');
    }

    return $data;
  }

public function exportExcel()
{
    try {
        // Reutilizar la misma lógica de filtrado
        $query = Oferta::query();
        $this->applyFilters($query);

        // Obtener TODOS los registros (sin paginación)
        $ofertas = $query->get();

        // Crear Excel
        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'Consecutivo', 'Objeto', 'Descripción', 'Moneda', 'Presupuesto',
            'Actividad ID', 'Fecha Inicio', 'Hora Inicio', 'Fecha Cierre', 'Hora Cierre', 'Estado'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $hoja->setCellValue($col . '1', $header);
            $col++;
        }
        $hoja->getStyle('A1:' . chr(ord('A') + count($headers) - 1) . '1')->getFont()->setBold(true);

        // Datos
        $fila = 2;
        foreach ($ofertas as $oferta) {
            $hoja->setCellValue('A' . $fila, $oferta->consecutivo);
            $hoja->setCellValue('B' . $fila, $oferta->objeto);
            $hoja->setCellValue('C' . $fila, $oferta->descripcion);
            $hoja->setCellValue('D' . $fila, $oferta->moneda);
            $hoja->setCellValue('E' . $fila, $oferta->presupuesto);
            $hoja->setCellValue('F' . $fila, $oferta->actividad_id);
            $hoja->setCellValue('G' . $fila, $oferta->fecha_inicio);
            $hoja->setCellValue('H' . $fila, $oferta->hora_inicio);
            $hoja->setCellValue('I' . $fila, $oferta->fecha_cierre);
            $hoja->setCellValue('J' . $fila, $oferta->hora_cierre);
            $hoja->setCellValue('K' . $fila, $oferta->estado);
            $fila++;
        }

        // Autoajustar columnas
        foreach (range('A', 'K') as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }

        // Descargar
        $filename = 'ofertas_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        error_log('Error en OfertaController@exportExcel: ' . $e->getMessage());
        // En exportación, no puedes enviar JSON fácilmente; mejor mostrar mensaje simple
        echo "Error al generar el reporte: " . htmlspecialchars($e->getMessage());
        exit(1);
    }
}
}
