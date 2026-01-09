<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
  protected $table = 'actividades';
  public $timestamps = true;

  const CREATED_AT = 'creado_en';
  const UPDATED_AT = 'actualizado_en';

  protected $fillable = [
    'codigo_segmento',
    'segmento',
    'codigo_familia',
    'familia',
    'codigo_clase',
    'clase',
    'codigo_producto',
    'producto'
  ];

  public function oferta()
  {
    return $this->hasOne(Oferta::class);
  }

}
