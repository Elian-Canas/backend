<?php

namespace App\Controllers;

use App\Models\OfertaDocumento;
use App\Validations\DocumentoValidator;
use App\Helpers\FileUploader;
use App\Helpers\Response;
use Exception;

class DocumentoController
{
    public function store()
    {
        try {
            $file = $_FILES['archivo'];
            $data = $_POST;

            DocumentoValidator::validate($file);

            $path = FileUploader::upload(
                $file,
                'uploads/documentos'
            );

            $doc = OfertaDocumento::create([
                'licitacion_id' => $data['licitacion_id'],
                'titulo'        => $data['titulo'],
                'descripcion'   => $data['descripcion'],
                'archivo'       => $path,
                'creado_en'     => date('Y-m-d H:i:s'),
            ]);

            Response::json($doc, 201);

        } catch (Exception $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}