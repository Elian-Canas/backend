<?

class DocumentoValidator
{
  public static function validate($file)
  {
    $allowed = ['application/pdf', 'application/zip'];

    if (!in_array($file['type'], $allowed)) {
      throw new Exception('Solo se permiten archivos PDF o ZIP');
    }
  }
}
