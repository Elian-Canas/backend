<?

namespace App\Validations;

class DocumentoValidator
{
  public static function validate($file)
  {
    $allowed = ['application/pdf', 'application/zip'];

    if (!in_array($file['type'], $allowed)) {
      throw new Exception('Solo se permiten archivos PDF o ZIP');
    }

    if (empty($_POST['licitacion_id']) || empty($_POST['titulo'])) {
      throw new Exception('Faltan datos requeridos (licitacion_id, titulo)');
    }
  }
}
