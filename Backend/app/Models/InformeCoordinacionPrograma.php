<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeCoordinacionPrograma extends Model
{
    protected $table = 'informe_coordinacion_programas';

    public $timestamps = true;

    protected $fillable = [
        'informe_coordinacion_id',
        'asignatura',
        'enlace_programa',
    ];

    public function informe()
    {
        return $this->belongsTo(InformeCoordinacion::class, 'informe_coordinacion_id');
    }
}
