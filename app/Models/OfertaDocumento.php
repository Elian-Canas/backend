<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaDocumento extends Model
{
  protected $table = 'ofertas_documentos';
  public $timestamps = true;

  const CREATED_AT = 'creado_en';
  const UPDATED_AT = 'actualizado_en';

  protected $fillable = [
    'licitacion_id',
    'titulo',
    'descripcion',
    'archivo'
  ];

  public function oferta()
  {
    return $this->belongsTo(Oferta::class, 'licitacion_id');
  }
}
