<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeCoordinacionAvance extends Model
{
    protected $table = 'informe_coordinacion_avances';

    public $timestamps = true;

    protected $fillable = [
        'informe_coordinacion_id',
        'curso_id',
        'docente_nombre',
        'asignatura',
        'seccion',
        'porcentaje_avance',
        'observaciones',
    ];

    protected $casts = [
        'porcentaje_avance' => 'integer',
    ];

    public function informe()
    {
        return $this->belongsTo(InformeCoordinacion::class, 'informe_coordinacion_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
