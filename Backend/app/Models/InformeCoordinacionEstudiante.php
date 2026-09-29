<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeCoordinacionEstudiante extends Model
{
    protected $table = 'informe_coordinacion_estudiantes';

    public $timestamps = true;

    protected $fillable = [
        'informe_coordinacion_id',
        'asignatura',
        'seccion',
        'cantidad_estudiantes',
        'carne_estudiantes',
    ];

    protected $casts = [
        'cantidad_estudiantes' => 'integer',
    ];

    public function informe()
    {
        return $this->belongsTo(InformeCoordinacion::class, 'informe_coordinacion_id');
    }
}
