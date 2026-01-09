<?php

namespace App\Helpers;

use Illuminate\Database\Capsule\Manager as DB;

class GeneratorConsecutive
{

  const PREFIJO = 'PO';
  /**
   * Genera un consecutivo del tipo PO-0001-25
   *
   * @return string
   * @throws \Exception Si no se puede generar el consecutivo
   */
  public static function generar(): string
  {
    $anio = date('Y');
    $prefijo = self::PREFIJO;

    DB::beginTransaction();
    try {
      $contador = DB::table('contadores')
        ->where('anio', $anio)
        ->lockForUpdate()
        ->first();

      if (!$contador) {
        // Insertar primer contador del año
        DB::table('contadores')->insert([
          'nombre' => 'oferta',
          'anio' => $anio,
          'ultimo_valor' => 0
        ]);
        $nuevoValor = 1;
      } else {
        $nuevoValor = $contador->ultimo_valor + 1;
      }

      DB::table('contadores')
        ->where('nombre', 'oferta')
        ->where('anio', $anio)
        ->update(['ultimo_valor' => $nuevoValor]);

      DB::commit();
      $anioCorto = substr($anio, -2); // "2026" → "26"

      return sprintf('%s-%04d-%s', $prefijo, $nuevoValor, $anioCorto);
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
  }
}
