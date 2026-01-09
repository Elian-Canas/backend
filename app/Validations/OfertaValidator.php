<?

namespace App\Validations;

use Exception;

class OfertaValidator
{
  public static function validate(array $data)
  {
    if (empty($data['objeto']) || strlen($data['objeto']) > 150) {
      throw new Exception('Objeto inválido');
    }

    if (empty($data['descripcion']) || strlen($data['descripcion']) > 400) {
      throw new Exception('Descripción inválida');
    }

    if (empty($data['moneda'])) {
      throw new Exception('Moneda inválida');
    }

    if (!is_numeric($data['presupuesto'])) {
      throw new Exception('Presupuesto inválido');
    }

    if (!is_int($data['actividad_id']) && !ctype_digit((string) $data['actividad_id'])) {
      throw new Exception('ID de actividad inválido');
    }
    $actividadId = (int) $data['actividad_id'];

    
    $inicio = strtotime($data['fecha_inicio'] . ' ' . $data['hora_inicio']);
    $cierre = strtotime($data['fecha_cierre'] . ' ' . $data['hora_cierre']);

    if ($inicio >= $cierre) {
      throw new Exception('La fecha/hora de inicio debe ser menor a la de cierre');
    }
  }
}
