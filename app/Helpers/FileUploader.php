<?php

namespace App\Helpers;

class FileUploader
{
    /**
     * Sube un archivo a un directorio específico.
     *
     * @param array $file El archivo desde $_FILES['campo']
     * @param string $directory Directorio relativo desde la raíz del proyecto (ej: 'documentos')
     * @param array $allowedExtensions Extensiones permitidas (ej: ['pdf', 'zip'])
     * @param int $maxSizeBytes Tamaño máximo en bytes (ej: 10 * 1024 * 1024 = 10MB)
     * @return string Ruta relativa del archivo guardado (ej: 'documentos/abc123_contrato.pdf')
     * @throws \Exception Si hay error
     */
    public static function upload(
        array $file,
        string $directory = 'uploads',
        array $allowedExtensions = ['pdf', 'zip'],
        int $maxSizeBytes = 10485760 // 10 MB
    ): string {
        // Validar que el archivo no tenga errores
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Error al subir el archivo: ' . self::getErrorMessage($file['error']));
        }

        // Validar tamaño
        if ($file['size'] > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / 1024 / 1024, 1);
            throw new \Exception("El archivo excede el tamaño máximo permitido ({$maxMb} MB)");
        }

        // Obtener extensión
        $originalName = $file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Validar extensión
        if (!in_array($extension, $allowedExtensions)) {
            $allowedList = implode(', ', $allowedExtensions);
            throw new \Exception("Tipo de archivo no permitido. Solo se aceptan: {$allowedList}");
        }

        // Crear nombre único
        $uniqueName = uniqid() . '_' . self::sanitizeFileName($originalName);
        $relativePath = rtrim($directory, '/') . '/' . $uniqueName;
        $absolutePath = __DIR__ . '/../../' . $relativePath;

        // Crear directorio si no existe
        $uploadDir = dirname($absolutePath);
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception("No se pudo crear el directorio de subida: {$uploadDir}");
            }
        }

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            throw new \Exception("No se pudo guardar el archivo en el servidor");
        }

        // Asegurar permisos
        chmod($absolutePath, 0644);

        return $relativePath; // Ruta relativa desde la raíz del proyecto
    }

    /**
     * Limpia el nombre del archivo para evitar problemas de seguridad.
     */
    private static function sanitizeFileName(string $filename): string
    {
        // Eliminar caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        // Evitar nombres como "...."
        $filename = trim($filename, '.');
        return $filename ?: 'archivo';
    }

    /**
     * Convierte código de error de PHP a mensaje legible.
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'El archivo excede el límite definido en php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo excede el límite definido en el formulario';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo fue subido parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se seleccionó ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta la carpeta temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo en disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Extensión detuvo la subida';
            default:
                return 'Error desconocido';
        }
    }
}